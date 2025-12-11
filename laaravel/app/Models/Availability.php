<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use CrudTrait;
    protected $fillable = [
        'user_id',
        'name',
        'designation',
        'venue',
        'time_slot',
    ];

    protected static function booted()
    {
        static::creating(function ($availability) {
            $user = backpack_auth()->user();
            if ($user) {
                $availability->user_id = $user->id;
            }
        });

        static::updating(function ($availability) {
            $user = backpack_auth()->user();
            if ($user) {
                $availability->user_id = $user->id;
            }
        });
    }
}
