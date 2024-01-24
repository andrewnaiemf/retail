<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends Authenticatable  implements JWTSubject
{
    use HasFactory;
    use LaratrustUserTrait;
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'id',
        'name',
        'organization',
        'email',
        'password',
        'phone_number',
        'tax_number',
        'status',
    ];

    protected $hidden = ['password'];

    public function toArray()
    {

        $userArray = parent::toArray();
        $userArray = array_merge($userArray,[
            'balance' => $this->balance,
            'overdue' => $this->overdue,
            'total_invoices_count' => $this->total_invoices_count,
            'total_invoices_amount' => $this->total_invoices_amount,
            'total_out_standing' => $this->total_out_standing
        ]);
        return $userArray;
    }

    protected $append = ['balance', 'overdue', 'total_invoices_count', 'total_invoices_amount', 'tota_out_standing'];


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

    public function billingAddress()
    {
        return $this->hasOne(BillingAddress::class ,'contact_id');
    }

    public function shippingAddress()
    {
        return $this->hasOne(ShippingAddress::class, 'contact_id');
    }

    public function invoices(){
        return $this->hasMany(Invoice::class, 'contact_id');
    }

    public function receipts(){
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
}