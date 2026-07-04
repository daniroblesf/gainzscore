<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'name', 'category', 'image', 'xp_multiplier'];

    public function workoutSets()
    {
        return $this->hasMany(WorkoutSet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
