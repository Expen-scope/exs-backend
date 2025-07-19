<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_transaction',
        'category',
        'price',
        'company_id',
        'user_id',
        'description',
        'currency',
        'source',
        'repeat',
        'date',
    ];
    public function user()
    {

        return $this->belongsTo(User::class);
    }
    public function company()
    {

        return $this->belongsTo(Company::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
