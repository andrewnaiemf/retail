<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use LaratrustUserTrait;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'device_token',
        'organization',
        'email',
        'password',
        'phone_number',
        'tax_number',
        'status',
        'locale'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


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

    public function shippingAddress()
    {
        return $this->hasOne(ShippingAddress::class, 'contact_id');
    }

    public function customer(){
        return $this->hasOne(Customer::class);
    }
}
