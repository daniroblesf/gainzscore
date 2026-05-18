<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutSet extends Model
{
    protected $fillable = [
        'workout_id',
        'exercise_id',
        'set_number',
        'weight',
        'reps',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'weight' => 'float',
    ];

    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}
