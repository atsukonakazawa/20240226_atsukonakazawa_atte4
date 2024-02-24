<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Work extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'user_id',
        'workDate',
        'workIn',
        'workOut',
        'wholeWorkTime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

        public function breaktime()
    {
        return $this->hasMany(breaktime::class);
    }


}
