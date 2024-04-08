<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'description',
        'issue_date',
        'due_date',
        'due_amount',
        'paid_amount',
        'total',
        'contact_id',
        'status',
        'reference',
        'notes',
        'terms_conditions',
        'qrcode_string',
        'payment_method',
        'pdf'
    ];

    public function toArray()
    {
        $invoice = parent::toArray();
        $invoice['status'] = $this->status == 'Paid' ? trans('locale.Paid') : $this->status ;
        $invoice['payment_method'] = $this->due_amount == 0 ? trans('locale.isPaid') : ($this->payment_method == -1 ? trans('locale.in_advance') : $this->status);

        $invoice['inventory'] = Inventory::find(1);
        $invoice = array_merge($invoice, ['type' => trans('locale.tax_invoice'), 'owner' => $this->getOwnerAttribute()]);
        return $invoice;
    }



    public function getOwnerAttribute() //owner
    {
//        $owner = User::whereRoleIs('administrator')->with('shippingAddress')->first();
//        $owner->commercial_registration_number = '1010839238';
        $owner = Customer::find($this->contact_id);
        return $owner;
    }

    public function contact() //customer
    {
        return $this->belongsTo(Customer::class, 'contact_id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function lineItems()
    {
        return $this->hasMany(LineItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Allocation::class, 'allocatee_id');
    }
}
