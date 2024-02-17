<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name_ar',
        'name_en',
        'description',
        'category_id',
        'type',
        'unit_type',
        'unit',
        'buying_price',
        'selling_price',
        'is_buying_price_inclusive',
        'is_selling_price_inclusive',
        'sku',
        'barcode',
        'is_sold',
        'is_bought',
        'track_quantity',
        'tax_id',
        'special_tax_reason_id',
        'pos_product'
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class)->withPivot('stock');
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class)->withPivot('price');
    }

    public function tax(){
        return $this->belongsTo(Tax::class);
    }
}
