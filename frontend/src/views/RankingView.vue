<script setup>
import { ref, onMounted } from 'vue'

// ── Ranking data ────────────────────────────────────────────────────────────
// TODO: Replace this static array with the real backend call:
//   const res   = await fetch('http://localhost:8000/api/ranking', {
//     headers: { 'Accept': 'application/json' },
//   })
//   const data  = await res.json()
//   players.value = data.data   // array of players sorted by XP desc
//
// Each player returned should have: { pos, name, level, rank, total_xp }
const players = ref([
  { pos: 1, name: 'IronBeast',    level: 8, rank: 'DIAMOND',     total_xp: 29800 },
  { pos: 2, name: 'GoldGunther',  level: 6, rank: 'PLATINUM I',  total_xp: 17100 },
  { pos: 3, name: 'SilverStreak', level: 5, rank: 'GOLD II',     total_xp: 10400 },
  { pos: 4, name: 'GainzPlayer',  level: 3, rank: 'SILVER I',    total_xp: 3650  },
  { pos: 5, name: 'BronzeBull',   level: 2, rank: 'BRONZE III',  total_xp: 1300  },
])

const loading = ref(false)

// TODO: Uncomment and connect once the backend is ready:
// onMounted(async () => {
//   loading.value = true
//   try {
//     const res  = await fetch('http://localhost:8000/api/ranking', {
//       headers: { 'Accept': 'application/json' },
//     })
//     const data = await res.json()
//     players.value = data.data
//   } catch (e) {
//     console.error('Could not load ranking:', e)
//   } finally {
//     loading.value = false
//   }
// })

// ── Rank badge config ────────────────────────────────────────────────────────
function rankMeta(rankName) {
  if (rankName.startsWith('DIAMOND'))   return { icon: '💎', color: 'text-cyan-300',   border: 'border-cyan-400/40',   bg: 'bg-cyan-400/10'   }
  if (rankName.startsWith('PLATINUM'))  return { icon: '🔷', color: 'text-purple-300', border: 'border-purple-400/40', bg: 'bg-purple-400/10' }
  if (rankName.startsWith('GOLD'))      return { icon: '🥇', color: 'text-yellow-300', border: 'border-yellow-400/40', bg: 'bg-yellow-400/10' }
  if (rankName.startsWith('SILVER'))    return { icon: '🥈', color: 'text-neon-green',  border: 'border-neon-green/40',  bg: 'bg-neon-green/10'  }
  return                                       { icon: '🥉', color: 'text-orange-300', border: 'border-orange-400/40', bg: 'bg-orange-400/10' }
}

function posLabel(pos) {
  if (pos === 1) return '🥇'
  if (pos === 2) return '🥈'
  if (pos === 3) return '🥉'
  return `#${pos}`
}
</script>

<template>
  <div class="max-w-md mx-auto px-4 py-6 space-y-4">

    <!-- ── Header ── -->
    <header class="space-y-3">
      <router-link
        to="/home"
        class="inline-flex items-center gap-1 text-[10px] tracking-widest text-white/40
               uppercase hover:text-neon-green transition-colors"
      >
        ← Home
      </router-link>
      <div class="flex items-center justify-between">
        <div>
          <p class="text-[10px] tracking-widest text-white/30 uppercase">League</p>
          <h1 class="text-xl font-bold leading-none">
            <span class="text-neon-green">RANK</span><span class="text-white">ING</span>
          </h1>
        </div>
        <div class="text-right">
          <p class="text-[10px] tracking-widest text-white/30 uppercase">Season 1</p>
          <p class="text-neon-green text-sm font-bold">Live</p>
        </div>
      </div>
    </header>

    <!-- ── Loading skeleton ── -->
    <div v-if="loading" class="space-y-2">
      <div v-for="i in 5" :key="i"
           class="bg-dark-card border border-white/5 rounded-2xl h-16 animate-pulse" />
    </div>

    <!-- ── Ranking table ── -->
    <div v-else class="space-y-2">

      <!-- Column labels -->
      <div class="grid grid-cols-[2.5rem_1fr_auto] gap-x-3 px-4 mb-1">
        <span class="text-[9px] tracking-widest text-white/30 uppercase text-center">#</span>
        <span class="text-[9px] tracking-widest text-white/30 uppercase">Player</span>
        <span class="text-[9px] tracking-widest text-white/30 uppercase text-right">Total XP</span>
      </div>

      <!-- Player rows -->
      <div
        v-for="player in players"
        :key="player.pos"
        class="bg-dark-card border border-white/10 rounded-2xl px-4 py-3
               grid grid-cols-[2.5rem_1fr_auto] gap-x-3 items-center
               transition-colors hover:border-white/20"
        :class="player.pos === 1 ? 'border-cyan-400/30 shadow-[0_0_16px_rgba(103,232,249,0.08)]' : ''"
      >
        <!-- Position -->
        <span class="text-base font-bold text-center tabular-nums"
              :class="player.pos <= 3 ? '' : 'text-white/30 text-sm'">
          {{ posLabel(player.pos) }}
        </span>

        <!-- Name + rank badge -->
        <div class="min-w-0">
          <p class="text-sm font-bold text-white truncate">{{ player.name }}</p>
          <div class="flex items-center gap-1.5 mt-0.5">
            <span class="text-xs">{{ rankMeta(player.rank).icon }}</span>
            <span
              class="text-[10px] font-bold tracking-wider px-1.5 py-0.5 rounded border"
              :class="[rankMeta(player.rank).color, rankMeta(player.rank).border, rankMeta(player.rank).bg]"
            >
              {{ player.rank }}
            </span>
            <span class="text-[10px] text-white/30">Lv.{{ player.level }}</span>
          </div>
        </div>

        <!-- Total XP -->
        <div class="text-right">
          <p class="text-neon-green text-sm font-bold tabular-nums">
            {{ player.total_xp.toLocaleString() }}
          </p>
          <p class="text-[9px] text-white/30 uppercase tracking-widest">XP</p>
        </div>
      </div>
    </div>

    <!-- ── Nav back ── -->
    <router-link
      to="/tracker"
      class="block text-center py-3 rounded-xl border border-dashed border-white/20
             text-xs font-bold tracking-widest text-white/40 uppercase
             hover:border-neon-green hover:text-neon-green transition-colors"
    >
      ← Back to Tracker
    </router-link>

  </div>
</template>
