<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "price",
        "collectedoprice",
        "time",
        "user_id",
    ];
    protected $casts = [
        'time' => 'datetime',
        'price' => 'float',
        'collectedoprice' => 'float',
    ];
public function user()
    {
        return $this->belongsTo(User::class);
    }
}
