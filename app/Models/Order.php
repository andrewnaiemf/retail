<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'driver_id', 'shipping_status', 'reference', 'status', 'inventory_id', 'notes', 'terms_conditions',
    ];

    public function toArray()
    {
        $orderArray = parent::toArray();
        $orderArray = array_merge($orderArray,[
            'total_tax' => (float) $this->total_tax,
            'total' => (float) $this->total,
            'total_with_tax' => $this->total_with_tax,
        ]);

        return $orderArray;
    }

    protected function getTotalTaxAttribute(){
        $totalTax = $this->orderItems()->sum(DB::raw('quantity * unit_price * 0.15'));
        return round($totalTax, 2);

    }

    protected function getTotalWithTaxAttribute(){
        return $this->total_tax  + $this->total;
    }

    protected function getTotalAttribute(){
        return $this->orderItems()->sum('unit_price');
    }


    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function driver(){
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->orderBy('id', 'desc');
    }
}
