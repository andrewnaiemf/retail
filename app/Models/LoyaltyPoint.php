<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    use HasFactory;

    protected $fillable = ['customer_type', 'points', 'discount_amount', 'status', 'customer_category_id'];

    public function customerCategory()
    {
        return $this->belongsTo(CustomerCategory::class, 'customer_category_id');
    }

}
