<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'start_date', 'end_date'];

    public function getStartDateAttribute($date)
    {
        return Carbon::parse($date);
    }

    public function getEndDateAttribute($date)
    {
        return Carbon::parse($date);
    }
}
