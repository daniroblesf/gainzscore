<script setup>
import { computed, onMounted, ref } from 'vue'
import {
  Gem,
  Shield,
  Trophy,
  Medal,
  Dumbbell,
  Crown,
  Zap,
  Flame,
  RefreshCw,
  Users,
} from '@lucide/vue'
import { API_BASE_URL } from '../utils/api'

const players = ref([])
const isLoading = ref(true)
const errorMessage = ref('')

const topPlayers = computed(() => players.value.slice(0, 3))
const restPlayers = computed(() => players.value.slice(3))
const leader = computed(() => players.value[0] || null)
const totalPlayers = computed(() => players.value.length)
const totalLeagueXp = computed(() => players.value.reduce((sum, player) => sum + Number(player.total_xp || 0), 0))

onMounted(() => {
  loadRanking()
})

async function loadRanking() {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch(`${API_BASE_URL}/ranking`)

    if (!response.ok) {
      throw new Error('Ranking konnte nicht geladen werden.')
    }

    const data = await response.json()
    players.value = data.data
  } catch {
    errorMessage.value = 'Ranking konnte nicht geladen werden.'
  } finally {
    isLoading.value = false
  }
}

function rankMeta(rankName = '') {
  if (rankName.startsWith('DIAMOND')) return { icon: Gem, color: 'text-cyan-300', border: 'border-cyan-400/40', bg: 'bg-cyan-400/10' }
  if (rankName.startsWith('PLATINUM')) return { icon: Shield, color: 'text-purple-300', border: 'border-purple-400/40', bg: 'bg-purple-400/10' }
  if (rankName.startsWith('GOLD')) return { icon: Trophy, color: 'text-yellow-300', border: 'border-yellow-400/40', bg: 'bg-yellow-400/10' }
  if (rankName.startsWith('SILVER')) return { icon: Medal, color: 'text-neon-green', border: 'border-neon-green/40', bg: 'bg-neon-green/10' }
  return { icon: Dumbbell, color: 'text-orange-300', border: 'border-orange-400/40', bg: 'bg-orange-400/10' }
}

function posIcon(pos) {
  if (pos === 1) return Crown
  if (pos === 2) return Zap
  if (pos === 3) return Flame
  return null
}

function podiumClass(pos) {
  if (pos === 1) return 'border-yellow-300/40 bg-yellow-300/10'
  if (pos === 2) return 'border-cyan-300/35 bg-cyan-300/10'
  return 'border-orange-300/35 bg-orange-300/10'
}

function formatXp(value) {
  return Number(value || 0).toLocaleString()
}
</script>

<template>
  <div class="page-shell">
    <header class="space-y-3">
      <router-link
        to="/home"
        class="inline-flex items-center gap-1 text-[10px] tracking-widest text-white/40
               uppercase hover:text-neon-green transition-colors"
      >
        &larr; Home
      </router-link>

      <div class="flex items-center justify-between">
        <div>
          <p class="text-[10px] tracking-widest text-white/30 uppercase">League</p>
          <h1 class="text-xl font-bold leading-none">
            <span class="text-neon-green">RANK</span><span class="text-white">ING</span>
          </h1>
        </div>

        <button
          class="w-10 h-10 rounded-xl border border-white/10 bg-black/35 text-white/45
                 flex items-center justify-center hover:text-neon-green hover:border-neon-green/50
                 transition-colors active:scale-95 disabled:opacity-40"
          :disabled="isLoading"
          title="Ranking aktualisieren"
          @click="loadRanking"
        >
          <RefreshCw class="w-4 h-4" :class="isLoading ? 'animate-spin' : ''" stroke-width="2.4" />
        </button>
      </div>
    </header>

    <div v-if="isLoading" class="space-y-3">
      <div class="card h-28 animate-pulse"></div>
      <div class="card h-20 animate-pulse"></div>
      <div class="card h-20 animate-pulse"></div>
    </div>

    <div v-else-if="errorMessage" class="card text-center space-y-3">
      <p class="text-xs tracking-widest uppercase text-red-300">
        {{ errorMessage }}
      </p>
      <button class="primary-action" @click="loadRanking">
        Erneut laden
      </button>
    </div>

    <template v-else>
      <section class="card space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-[10px] tracking-widest uppercase text-white/30">Season 1</p>
            <p class="text-lg font-bold text-white">Live Standings</p>
          </div>

          <div class="w-11 h-11 rounded-xl border border-neon-green/30 bg-neon-green/10 flex items-center justify-center">
            <Trophy class="w-5 h-5 text-neon-green" stroke-width="2.4" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div class="rounded-xl border border-white/10 bg-white/5 p-3">
            <div class="flex items-center gap-2 text-white/35">
              <Users class="w-4 h-4" stroke-width="2.4" />
              <p class="text-[9px] tracking-widest uppercase">Players</p>
            </div>
            <p class="mt-2 text-2xl font-extrabold text-white tabular-nums">{{ totalPlayers }}</p>
          </div>

          <div class="rounded-xl border border-white/10 bg-white/5 p-3 text-right">
            <p class="text-[9px] tracking-widest uppercase text-white/35">League XP</p>
            <p class="mt-2 text-2xl font-extrabold text-neon-green tabular-nums">
              {{ formatXp(totalLeagueXp) }}
            </p>
          </div>
        </div>

        <div v-if="leader" class="rounded-xl border border-yellow-300/30 bg-yellow-300/10 p-3">
          <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
              <p class="text-[9px] tracking-widest uppercase text-yellow-200/60">Current Champion</p>
              <p class="text-base font-bold text-white truncate">{{ leader.name }}</p>
            </div>
            <p class="text-sm font-bold text-yellow-200 tabular-nums">{{ formatXp(leader.total_xp) }} XP</p>
          </div>
        </div>
      </section>

      <section v-if="topPlayers.length" class="space-y-2">
        <div class="flex items-center justify-between px-1">
          <p class="text-[10px] tracking-widest uppercase text-white/30">Podium</p>
          <p class="text-[10px] tracking-widest uppercase text-neon-green">Top 3</p>
        </div>

        <div
          v-for="player in topPlayers"
          :key="player.id"
          class="card grid grid-cols-[2.8rem_1fr_auto] gap-x-3 items-center"
          :class="podiumClass(player.pos)"
        >
          <div class="flex justify-center">
            <component
              :is="posIcon(player.pos)"
              class="w-6 h-6"
              :class="player.pos === 1 ? 'text-yellow-300' : player.pos === 2 ? 'text-cyan-300' : 'text-orange-300'"
              stroke-width="2.4"
            />
          </div>

          <div class="min-w-0">
            <p class="text-sm font-bold text-white truncate">{{ player.name }}</p>
            <div class="flex items-center gap-1.5 mt-1">
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
              <span class="text-[10px] text-white/35">Lv.{{ player.level }}</span>
            </div>
          </div>

          <div class="text-right">
            <p class="text-neon-green text-sm font-bold tabular-nums">
              {{ formatXp(player.total_xp) }}
            </p>
            <p class="text-[9px] text-white/30 uppercase tracking-widest">XP</p>
          </div>
        </div>
      </section>

      <section class="space-y-2">
        <div class="grid grid-cols-[2.5rem_1fr_auto] gap-x-3 px-4 mb-1">
          <span class="text-[9px] tracking-widest text-white/30 uppercase text-center">#</span>
          <span class="text-[9px] tracking-widest text-white/30 uppercase">Player</span>
          <span class="text-[9px] tracking-widest text-white/30 uppercase text-right">Total XP</span>
        </div>

        <div
          v-for="player in restPlayers"
          :key="player.id"
          class="card grid grid-cols-[2.5rem_1fr_auto] gap-x-3 items-center transition-colors hover:border-white/20"
        >
          <div class="flex justify-center">
            <span class="text-sm font-bold text-white/30 tabular-nums">#{{ player.pos }}</span>
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
              {{ formatXp(player.total_xp) }}
            </p>
            <p class="text-[9px] text-white/30 uppercase tracking-widest">XP</p>
          </div>
        </div>

        <div v-if="players.length === 0" class="card py-12 text-center">
          <p class="text-[10px] tracking-widest uppercase text-white/25">
            Noch keine Spieler im Ranking
          </p>
        </div>
      </section>
    </template>

    <router-link
      to="/tracker"
      class="secondary-action block"
    >
      &larr; Back to Tracker
    </router-link>
  </div>
</template>
