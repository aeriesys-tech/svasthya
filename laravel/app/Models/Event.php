<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'topic',
        'activity_type',
        'start_date',
        'start_time',
        'created_by',
        'instructor',
        'venue',
        'status',
        'max_pax',
        'is_published',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'is_published' => 'boolean',
        'max_pax' => 'integer',
    ];
}
