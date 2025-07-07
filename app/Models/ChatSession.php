<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = ['sessionable_type', 'sessionable_id', 'token', 'expires_at'];

    public function sessionable()
    {
        return $this->morphTo();
    }
}
