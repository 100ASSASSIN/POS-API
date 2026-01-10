<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    // REQUIRED BY JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // REQUIRED BY JWT
    public function getJWTCustomClaims()
    {
        return [];
    }
}
