<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = ['company_id','type','start_date','end_date','active','reminder_sent_at'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
