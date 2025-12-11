<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use CrudTrait;
    protected $fillable = [
        'event_name',
        'start_date',
        'start_time',
        'venue',
        'session_by',
        'created_by',
        'updated_by',
    ];

    protected static function booted()
    {
        static::creating(function ($event) {
            $user = backpack_auth()->user();
            if ($user) {
                $event->created_by = $user->name;
            }
        });

        static::updating(function ($event) {
            $user = backpack_auth()->user();
            if ($user) {
                $event->updated_by = $user->name;
            }
        });
    }
}
