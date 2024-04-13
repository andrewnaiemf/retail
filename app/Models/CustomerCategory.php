<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCategory extends Model
{
    use HasFactory;
    protected $table = 'customer_categories';
    protected $fillable = ['name_en', 'name_ar'];

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'category_customer');
    }
}
