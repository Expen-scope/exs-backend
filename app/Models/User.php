<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token','token'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }



    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
                'ai_api_key'=>'hashed'

    ];
    public function goals()
    {
        return $this->hasMany(Goal::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }
}
