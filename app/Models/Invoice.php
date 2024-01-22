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
    ];

    public function contact()
    {
        return $this->belongsTo(User::class, 'contact_id');
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
