<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    use HasFactory;

    protected $fillable = ['id','amount', 'date', 'source_id', 'source_type', 'allocatee_id', 'allocatee_type'];

    public function source()
    {
        return $this->belongsTo(Receipt::class, 'source_id');
    }

    public function allocatee()
    {
        return $this->belongsToMany(Invoice::class, 'allocations', 'id', 'allocatee_id');
    }
}
