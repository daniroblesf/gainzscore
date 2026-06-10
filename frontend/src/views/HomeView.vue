<script setup>
const username      = 'GainzPlayer'
const totalWorkouts = 12
const totalXp       = 3650
</script>

<template>
  <div
    class="min-h-screen w-full bg-cover bg-center bg-no-repeat"
    style="background-image: linear-gradient(rgba(0,0,0,0.80), rgba(0,0,0,0.93)),
           url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1200&auto=format&fit=crop');"
  >
    <div class="max-w-md mx-auto px-4 py-6 space-y-6 text-white">

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

        <div class="bg-black/60 backdrop-blur-md border border-white/10 rounded-2xl p-4
                    flex flex-col justify-between h-28 shadow-lg">
          <div>
            <span class="text-lg">🏋️</span>
            <p class="text-[10px] font-bold tracking-widest text-white/40 uppercase mt-1">Workouts</p>
          </div>
          <p class="text-2xl font-extrabold text-white tabular-nums">{{ totalWorkouts }}</p>
        </div>

        <div class="bg-black/60 backdrop-blur-md border border-white/10 rounded-2xl p-4
                    flex flex-col justify-between h-28 shadow-lg">
          <div>
            <span class="text-lg">🔥</span>
            <p class="text-[10px] font-bold tracking-widest text-white/40 uppercase mt-1">Total XP</p>
          </div>
          <p class="text-2xl font-extrabold text-neon-green tabular-nums">{{ totalXp.toLocaleString() }}</p>
        </div>

      </div>

      <div class="space-y-3 pt-2">

        <router-link
          to="/tracker"
          class="block w-full py-4 rounded-2xl bg-neon-green text-dark-bg font-bold text-center
                 text-sm tracking-widest uppercase active:scale-95 transition-transform
                 shadow-[0_0_24px_rgba(34,255,119,0.3)] hover:shadow-[0_0_36px_rgba(34,255,119,0.45)]"
        >
          💪 Start Workout
        </router-link>

        <router-link
          to="/ranking"
          class="block w-full py-4 rounded-2xl bg-black/40 backdrop-blur-md border border-white/10
                 text-white font-bold text-center text-sm tracking-widest uppercase
                 active:scale-95 transition-all hover:border-neon-green/40 hover:text-neon-green"
        >
          🏆 View Leaderboard
        </router-link>

      </div>

    </div>
  </div>
</template>
