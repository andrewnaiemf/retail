<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'ar_name',
        'name',
        'account_id',
        'shipping_address_id',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('stock');
    }
}
