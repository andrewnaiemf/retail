<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'code',
        'name_en',
        'name_ar',
        'type_of_account',
        'parent_type',
        'balance',
        'type',
        'group_type',
        'receive_payments',
        'status',
    ];

    public function mainInventories(){
        return $this->hasMany(Inventory::class, 'account_id');
    }
}
