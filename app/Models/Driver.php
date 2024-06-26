<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Laratrust\Traits\LaratrustUserTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class Driver extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use LaratrustUserTrait;
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'device_token',
        'password',
        'phone_number',
        'locale'
    ];

    protected $hidden = ['password', 'organization', 'email', 'tax_number', 'remember_token'];

    public function toArray()
    {

        $driverArray = parent::toArray();
        if (Auth::user()){
            if (Auth::user()->hasRole('superadministrator') || Auth::user()->hasRole('administrator')) {
                $driverArray = array_merge($driverArray, [
                    'password' => $this->driverPassword->password,
                ]);
            }
        }

        return $driverArray;
    }

    public function getDeviceTokenAttribute($value)
    {
        // Check if the value is already an array
        if (is_array($value)) {
            return $value;
        }

        // Check if the value is a JSON string
        if (is_string($value) && json_decode($value) !== null) {
            return json_decode($value, true);
        }

        // If none of the above conditions match, return an empty array
        return [];

    }


        /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function driverPassword()
    {
        return $this->hasOne(DriverPassword::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders(){
        return $this->hasMany(Order::class);
    }


}
