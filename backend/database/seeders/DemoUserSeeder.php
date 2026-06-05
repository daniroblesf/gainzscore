<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Creates the demo users for the presentation.
     *
     * Rank thresholds (based on total XP accumulated across level-ups):
     *   Lv.1 →    0 XP total  → BRONZE I
     *   Lv.2 → 1000 XP total  → BRONZE III
     *   Lv.3 → 3000 XP total  → SILVER I
     *   Lv.4 → 6000 XP total  → SILVER III
     *   Lv.5 → 10000 XP total → GOLD II
     *   Lv.6 → 15000 XP total → PLATINUM I
     *   Lv.7 → 21000 XP total → PLATINUM II
     *   Lv.8 → 28000 XP total → DIAMOND
     */
    public function run(): void
    {
        $users = [
            // ── Demo user (for the login screen) ─────────────────────────────
            [
                'name'       => 'GainzPlayer',
                'email'      => 'demo@gainzscore.app',
                'password'   => Hash::make('password'),
                'current_xp' => 650,
                'level'      => 3,
                'rank'       => 'SILVER I',
            ],

            // ── Ranking competitors ───────────────────────────────────────────
            // IronBeast: level 8, DIAMOND rank — leaderboard leader
            [
                'name'       => 'IronBeast',
                'email'      => 'ironbeast@gainzscore.app',
                'password'   => Hash::make('password'),
                'current_xp' => 1800,
                'level'      => 8,
                'rank'       => 'DIAMOND',
            ],
            // GoldGunther: level 6, PLATINUM I — second place
            [
                'name'       => 'GoldGunther',
                'email'      => 'goldgunther@gainzscore.app',
                'password'   => Hash::make('password'),
                'current_xp' => 2100,
                'level'      => 6,
                'rank'       => 'PLATINUM I',
            ],
            // SilverStreak: level 5, GOLD II — third place
            [
                'name'       => 'SilverStreak',
                'email'      => 'silverstreak@gainzscore.app',
                'password'   => Hash::make('password'),
                'current_xp' => 400,
                'level'      => 5,
                'rank'       => 'GOLD II',
            ],
            // BronzeBull: level 2, BRONZE III — last place, shows the full progression arc
            [
                'name'       => 'BronzeBull',
                'email'      => 'bronzebull@gainzscore.app',
                'password'   => Hash::make('password'),
                'current_xp' => 300,
                'level'      => 2,
                'rank'       => 'BRONZE III',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
