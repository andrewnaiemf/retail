<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;

class Driver extends Authenticatable
{
    use HasFactory;
    use LaratrustUserTrait;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'password',
        'phone_number',
    ];

    protected $hidden = ['password', 'organization', 'email', 'tax_number', 'remember_token'];

    public function toArray()
    {

        $driverArray = parent::toArray();
        $driverArray = array_merge($driverArray,[
            'password' => $this->driverPassword->password,
        ]);
        return $driverArray;
    }

    public function driverPassword()
    {
        return $this->hasOne(DriverPassword::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
