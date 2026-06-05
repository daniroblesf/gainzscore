<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkoutSet;

class XpService
{
    /**
     * League rank thresholds: [minXP, name]
     */
    private const RANKS = [
        [0,    'BRONZE I'],
        [500,  'BRONZE II'],
        [1000, 'BRONZE III'],
        [2000, 'SILVER I'],
        [3500, 'SILVER II'],
        [5000, 'SILVER III'],
        [7000, 'GOLD I'],
        [9500, 'GOLD II'],
        [12500,'GOLD III'],
        [16000,'PLATINUM I'],
        [20000,'PLATINUM II'],
        [25000,'DIAMOND'],
    ];

    /**
     * XP required to reach next level (level * 1000).
     */
    public function xpForNextLevel(int $level): int
    {
        return $level * 1000;
    }

    /**
     * Calculate XP earned from a completed set.
     * Formula: XP = Volume(kg * reps) * exercise_multiplier
     */
    public function calculateSetXp(WorkoutSet $set): int
    {
        $volume = $set->weight * $set->reps;
        $multiplier = $set->exercise->xp_multiplier ?? 1.0;

        return (int) round($volume * $multiplier);
    }

    /**
     * Award XP to user and handle level-up + rank promotion.
     * Returns an array with xp_gained, leveled_up, and new_rank.
     */
    public function awardXp(User $user, int $xpGained): array
    {
        $user->current_xp += $xpGained;
        $leveledUp = false;

        while ($user->current_xp >= $this->xpForNextLevel($user->level)) {
            $user->current_xp -= $this->xpForNextLevel($user->level);
            $user->level++;
            $leveledUp = true;
        }

        $user->rank = $this->resolveRank($user->level);
        $user->save();

        return [
            'xp_gained'     => $xpGained,
            'leveled_up'    => $leveledUp,
            'current_xp'    => $user->current_xp,
            'level'         => $user->level,
            'rank'          => $user->rank,
            'xp_for_next'   => $this->xpForNextLevel($user->level),
        ];
    }

    private function resolveRank(int $level): string
    {
        $totalXpForLevel = 0;
        for ($i = 1; $i < $level; $i++) {
            $totalXpForLevel += $this->xpForNextLevel($i);
        }

        $rank = self::RANKS[0][1];
        foreach (self::RANKS as [$threshold, $name]) {
            if ($totalXpForLevel >= $threshold) {
                $rank = $name;
            }
        }

        return $rank;
    }
}
