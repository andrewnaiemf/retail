<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id', 'reference', 'issue_date', 'expiry_date', 'status', 'inventory_id', 'notes', 'terms_conditions',
    ];

    public function lineItems()
    {
        return $this->hasMany(LineItem::class);
    }
}
