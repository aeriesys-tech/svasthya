<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reflection extends Model
{
    protected $fillable = [
        'user_id',
        'q1',
        'q2',
        'q3',
        'q4',
        'q5',
        'q6',
        'date',
    ];

    protected $casts = [
        'q6' => 'integer',
        'date' => 'date',
    ];

    /**
     * Get the user that owns the reflection.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
