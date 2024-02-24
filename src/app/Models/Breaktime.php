<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Breaktime extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'user_id',
        'work_id',
        'breakDate',
        'breakIn',
        'breakOut',
        'wholeBreaktime'
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function Work()
    {
        return $this->belongsTo(Work::class);
    }


}
