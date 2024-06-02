<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptLoyalty extends Model
{
    use HasFactory;

    protected $table = 'receipt_loyality';

    protected $fillable = [
        'receipt_id',
        'customer_id',
        'points',
        'created_at'
    ];

    public $timestamps = false;

}
