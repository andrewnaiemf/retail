<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'order_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'tax_percent',
    ];


    public function toArray()
    {
        $orderArray = parent::toArray();
        $orderArray = array_merge($orderArray,[
            'total' => $this->total,
        ]);

        return $orderArray;
    }

    protected function getTotalAttribute(){
        return $this->quantity * $this->unit_price;
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
