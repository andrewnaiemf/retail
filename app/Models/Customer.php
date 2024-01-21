<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

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

    public function billingAddress()
    {
        return $this->hasOne(BillingAddress::class);
    }

    public function shippingAddress()
    {
        return $this->hasOne(ShippingAddress::class);
    }
}
