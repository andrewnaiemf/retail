<?php

namespace App\Models;

use App\Filter\StartsBetweenFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Spatie\QueryBuilder\AllowedFilter;

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
        $inventory = Inventory::first();
        $receipt = parent::toArray();
        $receipt['kind'] = $this->kind == 'received' ? trans('locale.received') : $this->kind ;
        $receipt['paid_amount'] = $this->amount;
        $receipt = array_merge($receipt, [
            'un_allocate_amount' => $this->getUnAllocateAmountAttribute(),
            'from_location' => $inventory
        ]);
        return $receipt;
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
