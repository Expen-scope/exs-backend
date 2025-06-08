<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "email",
        "password"
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

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
}
