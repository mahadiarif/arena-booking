<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecurrenceRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'interval', 'days_of_week',
        'end_type', 'end_date', 'end_after_count',
    ];

    protected $casts = [
        'days_of_week'   => 'array',
        'interval'       => 'integer',
        'end_after_count'=> 'integer',
        'end_date'       => 'date',
    ];
}
