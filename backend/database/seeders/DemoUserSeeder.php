<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'demo@gainzscore.app'],
            [
                'name' => 'GainzPlayer',
                'password' => Hash::make('password'),
                'current_xp' => 650,
                'level' => 3,
                'rank' => 'SILBER I',
            ]
        );
    }
}
