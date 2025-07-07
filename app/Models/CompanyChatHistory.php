<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyChatHistory extends Model
{
    use HasFactory;
    protected $fillable = ['company_id', 'role', 'content'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
