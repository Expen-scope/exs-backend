<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminAction extends Model
{
    use HasFactory;
    protected $fillable = [
        "admin_id",
        "action_type",
        "time",
        "target_id",
        "target_type"
    ];
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
