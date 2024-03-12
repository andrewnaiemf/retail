<?php

namespace App\Models;

use App\Filter\FiltersProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Str;

class Customer extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use LaratrustUserTrait;
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'id',
        'name',
        'device_token',
        'organization',
        'email',
        'password',
        'phone_number',
        'tax_number',
        'status',
        'locale'
    ];

    protected $hidden = ['password'];

    public function toArray()
    {
        $currentUrl = request()->url();
        $userArray = parent::toArray();

        if (!Str::contains($currentUrl, 'products')) {
            $userArray = array_merge($userArray, [
                'closing_balance' => (float)$this->balance,
                'overdue' => round($this->overdue, 2),
                'total_invoices_count' => $this->total_invoices_count,
                'total_invoices_amount' => (float)$this->total_invoices_amount,
                'total_out_standing' => (float)$this->total_out_standing,
                'total_paid' => (float)$this->total_out_standing - round($this->overdue, 2),
                'branches' => $this->branches,
                'shipping_address' => $this->shippingAddress,
                'billing_address' => $this->billingAddress,
            ]);
        }

        return $userArray;
    }

    public function getDeviceTokenAttribute($value)
    {
        // Check if the value is already an array
        if (is_array($value)) {
            return $value;
        }

        // Check if the value is a JSON string
        if (is_string($value) && json_decode($value) !== null) {
            return json_decode($value, true);
        }

        // If none of the above conditions match, return an empty array
        return [];

    }


    protected $append = ['billingAddress', 'shippingAddress', 'balance', 'overdue', 'total_invoices_count', 'total_invoices_amount', 'total_out_standing', 'total_paid', 'branches'];

    // protected $with = [];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getBalanceAttribute()
    {
        $balance = $this->invoices()->sum('due_amount');
        return $balance;
    }

    public function getOverdueAttribute()
    {
        $overdueInvoices = $this->invoices()->where('due_date', '<', now())->get();
        $overdueAmount = $overdueInvoices->sum('total') - $overdueInvoices->sum('paid_amount');

        return $overdueAmount;
    }

    public function getTotalInvoicesCountAttribute()
    {
        $count = $this->invoices()->count();
        return $count;
    }

    public function getTotalInvoicesAmountAttribute()
    {
        $amount = $this->invoices()->sum('total');
        return $amount;
    }

    public function getTotalOutStandingAttribute()
    {
        $amount = $this->invoices()->sum('due_amount');
        return $amount;
    }

    public function getBranchesAttribute()
    {
        $branches = null ;
        if ($this->tax_number) {
            $branches = User::where('tax_number', $this->tax_number)->get();
        }
        return  $branches;
    }

    public function billingAddress()
    {
        return $this->hasOne(BillingAddress::class, 'contact_id');
    }

    public function shippingAddress()
    {
        return $this->hasOne(ShippingAddress::class, 'contact_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'contact_id');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'contact_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('price');
    }

    public function orders(){
        return $this->hasMany(Order::class);
    }
}
