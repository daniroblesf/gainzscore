<script setup>
import { Gem, Shield, Trophy, Medal, Dumbbell, Crown, Zap, Flame } from '@lucide/vue'

const players = [
  { pos: 1, name: 'IronBeast',    level: 8, rank: 'DIAMOND',    total_xp: 29800 },
  { pos: 2, name: 'GoldGunther',  level: 6, rank: 'PLATINUM I', total_xp: 17100 },
  { pos: 3, name: 'SilverStreak', level: 5, rank: 'GOLD II',    total_xp: 10400 },
  { pos: 4, name: 'GainzPlayer',  level: 3, rank: 'SILVER I',   total_xp: 3650  },
  { pos: 5, name: 'BronzeBull',   level: 2, rank: 'BRONZE III', total_xp: 1300  },
]

function rankMeta(rankName) {
  if (rankName.startsWith('DIAMOND'))  return { icon: Gem,      color: 'text-cyan-300',   border: 'border-cyan-400/40',   bg: 'bg-cyan-400/10'   }
  if (rankName.startsWith('PLATINUM')) return { icon: Shield,   color: 'text-purple-300', border: 'border-purple-400/40', bg: 'bg-purple-400/10' }
  if (rankName.startsWith('GOLD'))     return { icon: Trophy,   color: 'text-yellow-300', border: 'border-yellow-400/40', bg: 'bg-yellow-400/10' }
  if (rankName.startsWith('SILVER'))   return { icon: Medal,    color: 'text-neon-green',  border: 'border-neon-green/40',  bg: 'bg-neon-green/10'  }
  return                                      { icon: Dumbbell, color: 'text-orange-300', border: 'border-orange-400/40', bg: 'bg-orange-400/10' }
}

function posIcon(pos) {
  if (pos === 1) return Crown
  if (pos === 2) return Zap
  if (pos === 3) return Flame
  return null
}
</script>

<template>
  <div class="max-w-md mx-auto px-4 py-6 space-y-4">

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

    <div class="space-y-2">

      <div class="grid grid-cols-[2.5rem_1fr_auto] gap-x-3 px-4 mb-1">
        <span class="text-[9px] tracking-widest text-white/30 uppercase text-center">#</span>
        <span class="text-[9px] tracking-widest text-white/30 uppercase">Player</span>
        <span class="text-[9px] tracking-widest text-white/30 uppercase text-right">Total XP</span>
      </div>

      <div
        v-for="player in players"
        :key="player.pos"
        class="bg-dark-card border border-white/10 rounded-2xl px-4 py-3
               grid grid-cols-[2.5rem_1fr_auto] gap-x-3 items-center
               transition-colors hover:border-white/20"
        :class="player.pos === 1 ? 'border-cyan-400/30 shadow-[0_0_16px_rgba(103,232,249,0.08)]' : ''"
      >
        <div class="flex justify-center">
          <component
            v-if="posIcon(player.pos)"
            :is="posIcon(player.pos)"
            class="w-5 h-5"
            :class="player.pos === 1 ? 'text-yellow-300' : player.pos === 2 ? 'text-cyan-300' : 'text-orange-300'"
            stroke-width="2.4"
          />
          <span v-else class="text-sm font-bold text-white/30 tabular-nums">#{{ player.pos }}</span>
        </div>

        <div class="min-w-0">
          <p class="text-sm font-bold text-white truncate">{{ player.name }}</p>
          <div class="flex items-center gap-1.5 mt-0.5">
            <span
              class="w-6 h-6 rounded-lg flex items-center justify-center border"
              :class="[rankMeta(player.rank).border, rankMeta(player.rank).bg]"
            >
              <component
                :is="rankMeta(player.rank).icon"
                class="w-3.5 h-3.5"
                :class="rankMeta(player.rank).color"
                stroke-width="2.4"
              />
            </span>
            <span
              class="text-[10px] font-bold tracking-wider px-1.5 py-0.5 rounded border"
              :class="[rankMeta(player.rank).color, rankMeta(player.rank).border, rankMeta(player.rank).bg]"
            >
              {{ player.rank }}
            </span>
            <span class="text-[10px] text-white/30">Lv.{{ player.level }}</span>
          </div>
        </div>

        <div class="text-right">
          <p class="text-neon-green text-sm font-bold tabular-nums">
            {{ player.total_xp.toLocaleString() }}
          </p>
          <p class="text-[9px] text-white/30 uppercase tracking-widest">XP</p>
        </div>
      </div>

    </div>

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
