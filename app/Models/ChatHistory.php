<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ChatHistory extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'chattable_id',
        'chattable_type',
        'role',
        'content',
    ];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function chattable(): MorphTo
    {
        return $this->morphTo();
    }
}
