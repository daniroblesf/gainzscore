<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $fillable = ['name', 'category', 'xp_multiplier'];

    public function workoutSets()
    {
        return $this->hasMany(WorkoutSet::class);
    }
}
