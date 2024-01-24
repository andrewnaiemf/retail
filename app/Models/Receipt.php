<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'reference',
        'description',
        'date',
        'amount',
        'kind',
        'contact_id',
        'account_id',
    ];


    public function toArray()
    {
        $from_location = 'Main Branch';
        
        if (app()->getLocale() == 'ar') {
            $from_location = 'المركز الرئيسي';
        }
        $invoice = parent::toArray();
        $invoice = array_merge($invoice, [
            'un_allocate_amount' => $this->getUnAllocateAmountAttribute(),
            'from_location' => $from_location
        ]);
        return $invoice;
    }

    public function getUnAllocateAmountAttribute()
    {
        $un_allocate_amount = $this->amount - $this->allocates()->sum('amount');
        return $un_allocate_amount;
    }

    public function contact()
    {
        return $this->belongsTo(Customer::class, 'contact_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function allocates(){
        return $this->hasMany(Allocation::class, 'source_id');
    }
}
