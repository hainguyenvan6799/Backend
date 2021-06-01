<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class ScheduleCalendar extends Model
{
    use HasFactory;
    protected $collection = "schedule_calendar";
}
