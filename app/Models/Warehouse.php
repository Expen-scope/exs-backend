<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
  use HasFactory;

    protected $fillable = [
        'company_id', 'name', 'category', 'quantity', 'location', 'status', 'last_updated'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }}
