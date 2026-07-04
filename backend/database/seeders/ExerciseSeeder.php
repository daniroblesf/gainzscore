<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        Exercise::truncate();

        $exercises = [
            ['category' => 'Chest', 'name' => 'Bankdruecken', 'image' => '/exercises/bench-press.png', 'xp_multiplier' => 1.2],
            ['category' => 'Chest', 'name' => 'Schraegbankdruecken', 'image' => '/exercises/incline-barbell-bench-press.png', 'xp_multiplier' => 1.2],
            ['category' => 'Chest', 'name' => 'Liegestuetze', 'image' => '/exercises/push-up.png', 'xp_multiplier' => 1.0],
            ['category' => 'Chest', 'name' => 'Kurzhantel Bankdruecken', 'image' => null, 'xp_multiplier' => 1.15],
            ['category' => 'Chest', 'name' => 'Cable Flys', 'image' => null, 'xp_multiplier' => 1.0],
            ['category' => 'Chest', 'name' => 'Dips', 'image' => null, 'xp_multiplier' => 1.15],

            ['category' => 'Back', 'name' => 'Latziehen', 'image' => '/exercises/lat-pulldown-machine.png', 'xp_multiplier' => 1.1],
            ['category' => 'Back', 'name' => 'Kurzhantelrudern', 'image' => '/exercises/db-row.png', 'xp_multiplier' => 1.1],
            ['category' => 'Back', 'name' => 'Klimmzuege', 'image' => '/exercises/pull-up.png', 'xp_multiplier' => 1.3],
            ['category' => 'Back', 'name' => 'Langhantelrudern', 'image' => null, 'xp_multiplier' => 1.2],
            ['category' => 'Back', 'name' => 'Kabelrudern', 'image' => null, 'xp_multiplier' => 1.1],
            ['category' => 'Back', 'name' => 'Face Pulls', 'image' => null, 'xp_multiplier' => 1.0],

            ['category' => 'Arms', 'name' => 'Bizeps Curls', 'image' => '/exercises/bicep-curl.png', 'xp_multiplier' => 1.0],
            ['category' => 'Arms', 'name' => 'Trizeps Druecken Overhead', 'image' => '/exercises/triceps-overhead.png', 'xp_multiplier' => 1.0],
            ['category' => 'Arms', 'name' => 'Hammer Curls', 'image' => '/exercises/hammer-curl.png', 'xp_multiplier' => 1.0],
            ['category' => 'Arms', 'name' => 'Trizeps Pushdowns', 'image' => null, 'xp_multiplier' => 1.0],
            ['category' => 'Arms', 'name' => 'Preacher Curls', 'image' => null, 'xp_multiplier' => 1.0],
            ['category' => 'Arms', 'name' => 'Skull Crushers', 'image' => null, 'xp_multiplier' => 1.05],

            ['category' => 'Legs', 'name' => 'Kniebeugen', 'image' => '/exercises/squats.png', 'xp_multiplier' => 1.3],
            ['category' => 'Legs', 'name' => 'Beinpresse', 'image' => '/exercises/leg-press.png', 'xp_multiplier' => 1.1],
            ['category' => 'Legs', 'name' => 'Ausfallschritte', 'image' => '/exercises/lunges.png', 'xp_multiplier' => 1.0],
            ['category' => 'Legs', 'name' => 'Rumaenisches Kreuzheben', 'image' => null, 'xp_multiplier' => 1.25],
            ['category' => 'Legs', 'name' => 'Beinstrecker', 'image' => null, 'xp_multiplier' => 1.0],
            ['category' => 'Legs', 'name' => 'Beinbeuger', 'image' => null, 'xp_multiplier' => 1.0],
            ['category' => 'Legs', 'name' => 'Wadenheben', 'image' => null, 'xp_multiplier' => 0.9],

            ['category' => 'Shoulders', 'name' => 'Schulterdruecken', 'image' => null, 'xp_multiplier' => 1.15],
            ['category' => 'Shoulders', 'name' => 'Seitheben', 'image' => null, 'xp_multiplier' => 0.95],
            ['category' => 'Shoulders', 'name' => 'Reverse Flys', 'image' => null, 'xp_multiplier' => 1.0],
            ['category' => 'Shoulders', 'name' => 'Frontheben', 'image' => null, 'xp_multiplier' => 0.95],
            ['category' => 'Shoulders', 'name' => 'Arnold Press', 'image' => null, 'xp_multiplier' => 1.1],

            ['category' => 'Core', 'name' => 'Plank', 'image' => null, 'xp_multiplier' => 0.8],
            ['category' => 'Core', 'name' => 'Crunches', 'image' => null, 'xp_multiplier' => 0.8],
            ['category' => 'Core', 'name' => 'Hanging Leg Raises', 'image' => null, 'xp_multiplier' => 1.0],
            ['category' => 'Core', 'name' => 'Russian Twists', 'image' => null, 'xp_multiplier' => 0.85],

            ['category' => 'Cardio', 'name' => 'Laufband', 'image' => null, 'xp_multiplier' => 0.75],
            ['category' => 'Cardio', 'name' => 'Bike Ergometer', 'image' => null, 'xp_multiplier' => 0.75],
            ['category' => 'Cardio', 'name' => 'Rudergeraet', 'image' => null, 'xp_multiplier' => 0.9],
            ['category' => 'Cardio', 'name' => 'Burpees', 'image' => null, 'xp_multiplier' => 1.0],
        ];

        Exercise::insert($exercises);
    }
}
