<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        // Wipe old data so re-seeding always gives a clean slate.
        Exercise::truncate();

        $exercises = [
            // Chest
            ['category' => 'Chest', 'name' => 'Bankdrücken',       'image' => '/exercises/bench-press.png',                    'xp_multiplier' => 1.2],
            ['category' => 'Chest', 'name' => 'Schrägbankdrücken', 'image' => '/exercises/incline-barbell-bench-press.png',    'xp_multiplier' => 1.2],
            ['category' => 'Chest', 'name' => 'Liegestütze',       'image' => '/exercises/push-up.png',                       'xp_multiplier' => 1.0],

            // Back
            ['category' => 'Back',  'name' => 'Latziehen',         'image' => '/exercises/lat-pulldown-machine.png',           'xp_multiplier' => 1.1],
            ['category' => 'Back',  'name' => 'Kurzhantelrudern',   'image' => '/exercises/db-row.png',                        'xp_multiplier' => 1.1],
            ['category' => 'Back',  'name' => 'Klimmzüge',         'image' => '/exercises/pull-up.png',                       'xp_multiplier' => 1.3],

            // Arms
            ['category' => 'Arms',  'name' => 'Bizeps Curls',               'image' => '/exercises/bicep-curl.png',           'xp_multiplier' => 1.0],
            ['category' => 'Arms',  'name' => 'Trizeps Drücken Overhead',   'image' => '/exercises/triceps-overhead.png',     'xp_multiplier' => 1.0],
            ['category' => 'Arms',  'name' => 'Hammer Curls',               'image' => '/exercises/hammer-curl.png',          'xp_multiplier' => 1.0],

            // Legs
            ['category' => 'Legs',  'name' => 'Kniebeugen',        'image' => '/exercises/squats.png',                        'xp_multiplier' => 1.3],
            ['category' => 'Legs',  'name' => 'Beinpresse',        'image' => '/exercises/leg-press.png',                     'xp_multiplier' => 1.1],
            ['category' => 'Legs',  'name' => 'Ausfallschritte',   'image' => '/exercises/lunges.png',                        'xp_multiplier' => 1.0],
        ];

        Exercise::insert($exercises);
    }
}
