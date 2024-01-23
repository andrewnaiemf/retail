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
