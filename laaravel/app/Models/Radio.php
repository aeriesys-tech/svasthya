<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Radio extends Model
{
    use CrudTrait;
    protected $fillable = [
        'title',
        'duration',
        'url',
        'session_by',
        'user_id',
    ];

    protected static function booted()
    {
        static::creating(function ($radio) {
            $user = backpack_auth()->user();
            if ($user) {
                $radio->user_id = $user->id;
            }
        });

        static::updating(function ($radio) {
            $user = backpack_auth()->user();
            if ($user) {
                $radio->user_id = $user->id;
            }
        });
    }
}
