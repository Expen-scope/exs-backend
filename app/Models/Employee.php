<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'monthly_salary',
        'position'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
