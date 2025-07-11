<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Company extends Authenticatable implements JWTSubject
{
      use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        "name",
        "email",
        "password"
    ];
    protected $hidden = [
        'password',
        'remember_token',
        'token'
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'ai_api_key'=>'hashed'
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }
    public function events()
    {
        return $this->hasMany(Event::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    public function chatHistories()
{
    return $this->hasMany(CompanyChatHistory::class);
}
public function warehouses()
{
    return $this->hasMany(Warehouse::class);
}

public function employees()
{
    return $this->hasMany(Employee::class);
}
}
