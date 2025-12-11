<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use CrudTrait;
    protected $fillable = [
        'appointee_name',
        'designation',
        'phone_number',
        'appointment_date',
        'appointment_time',
        'place',
        'status',
    ];
}
