<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverPassword extends Model
{
    use HasFactory;

    protected $fillable = ['password', 'driver_id'];

    protected $table = 'driver_passwords';
}
