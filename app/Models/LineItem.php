<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'invoice_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'discount',
        'discount_type',
        'tax_percent',
    ];

    public function toArray()
    {
        $lineItem = parent::toArray();
        $name = 'name_' . app()->getLocale();
        $lineItemArray = array_merge($lineItem,[
            'name' => $this->product->$name ?? '',
        ]);
        return $lineItemArray;
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
