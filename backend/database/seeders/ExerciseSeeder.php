<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $exercises = [
            ['name' => 'Bankdrücken',   'category' => 'Chest', 'xp_multiplier' => 1.2],
            ['name' => 'Latziehen',     'category' => 'Back',  'xp_multiplier' => 1.1],
            ['name' => 'Kniebeugen',    'category' => 'Legs',  'xp_multiplier' => 1.3],
            ['name' => 'Bizeps Curls',  'category' => 'Arms',  'xp_multiplier' => 1.0],
        ];

        foreach ($exercises as $exercise) {
            Exercise::firstOrCreate(['name' => $exercise['name']], $exercise);
        }
    }
}
