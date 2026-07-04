<script setup>
import { Dumbbell, Flame, Trophy } from '@lucide/vue'

const username = 'GainzPlayer'
const totalWorkouts = 12
const totalXp = 3650
</script>

<template>
  <div class="min-h-screen w-full">
    <div class="page-shell space-y-6 text-white">
      <header class="flex items-center justify-between pt-4">
        <div>
          <p class="text-[10px] tracking-widest text-white/30 uppercase">Welcome back</p>
          <h1 class="text-xl font-bold leading-none">
            <span class="text-neon-green">HALLO </span>
            <span class="text-white">{{ username }}</span>
          </h1>
        </div>

        <h2 class="text-xl font-bold leading-none">
          <span class="text-neon-green">GAINZ</span><span class="text-white">SCORE</span>
        </h2>
      </header>

      <div class="grid grid-cols-2 gap-4">
        <div class="card flex flex-col justify-between h-28">
          <div>
            <Dumbbell class="w-5 h-5 text-neon-green" stroke-width="2.4" />
            <p class="text-[10px] font-bold tracking-widest text-white/40 uppercase mt-2">Workouts</p>
          </div>
          <p class="text-2xl font-extrabold text-white tabular-nums">{{ totalWorkouts }}</p>
        </div>

        <div class="card flex flex-col justify-between h-28">
          <div>
            <Flame class="w-5 h-5 text-neon-green" stroke-width="2.4" />
            <p class="text-[10px] font-bold tracking-widest text-white/40 uppercase mt-2">Total XP</p>
          </div>
          <p class="text-2xl font-extrabold text-neon-green tabular-nums">{{ totalXp.toLocaleString() }}</p>
        </div>
      </div>

      <div class="space-y-3 pt-2">
        <router-link
          to="/tracker"
          class="primary-action flex items-center justify-center gap-2"
        >
          <Dumbbell class="w-5 h-5" stroke-width="2.4" />
          <span>Start Workout</span>
        </router-link>

        <router-link
          to="/ranking"
          class="secondary-action flex items-center justify-center gap-2"
        >
          <Trophy class="w-5 h-5" stroke-width="2.4" />
          <span>View Leaderboard</span>
        </router-link>
      </div>
    </div>
  </div>
</template>
