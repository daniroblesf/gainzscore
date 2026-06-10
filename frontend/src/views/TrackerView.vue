<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { Gem, Shield, Trophy, Medal, Dumbbell } from '@lucide/vue'
import ProgressBar from '../components/ProgressBar.vue'
import ExerciseCard from '../components/ExerciseCard.vue'

const today   = new Date().toISOString().slice(0, 10)
const dayName = new Date().toLocaleDateString('de-DE', { weekday: 'long' })

const user = ref({
  name:      'GainzPlayer',
  rank:      'SILVER I',
  level:     3,
  currentXp: 650,
  xpForNext: 1000,
})

function rankIcon(rankName) {
  if (rankName.startsWith('DIAMOND'))  return Gem
  if (rankName.startsWith('PLATINUM')) return Shield
  if (rankName.startsWith('GOLD'))     return Trophy
  if (rankName.startsWith('SILVER'))   return Medal
  return Dumbbell
}

const exercises = ref([
  { id: 1, name: 'Bankdrücken'  },
  { id: 2, name: 'Latziehen'    },
  { id: 3, name: 'Kniebeugen'   },
  { id: 4, name: 'Bizeps Curls' },
])

function addExercise() {
  const name = prompt('Übungsname eingeben:')
  if (name?.trim()) {
    exercises.value.push({ id: Date.now(), name: name.trim() })
  }
}
</script>

<template>
  <div class="max-w-md mx-auto px-4 py-6 space-y-4">

    <header class="space-y-3">
      <RouterLink
        to="/home"
        class="inline-flex items-center gap-1 text-[10px] tracking-widest text-white/40
               uppercase hover:text-neon-green transition-colors"
      >
        ← Home
      </RouterLink>
      <div class="flex items-center justify-between">
        <div>
          <p class="text-[10px] tracking-widest text-white/30 uppercase">Training</p>
          <h1 class="text-xl font-bold leading-none">
            <span class="text-neon-green">GAINZ</span><span class="text-white">SCORE</span>
          </h1>
        </div>
        <div class="flex items-center gap-3">
          <RouterLink
            to="/ranking"
            class="text-[10px] tracking-widest text-white/40 uppercase border border-white/10
                   rounded-lg px-2.5 py-1.5 hover:border-neon-green hover:text-neon-green transition-colors"
          >
            League
          </RouterLink>
          <div class="text-right">
            <p class="text-neon-green text-sm font-bold tabular-nums">{{ today }}</p>
            <p class="text-white/30 text-[10px] capitalize">{{ dayName }}</p>
          </div>
        </div>
      </div>
    </header>

    <div class="card">
      <div class="flex items-center justify-between mb-3">
        <div>
          <p class="text-[10px] tracking-widest text-white/30 uppercase mb-0.5">Level {{ user.level }}</p>
          <p class="text-sm font-bold text-white">{{ user.name }}</p>
        </div>
        <div class="flex items-center gap-1.5 bg-neon-green/10 border border-neon-green/30 rounded-lg px-3 py-1.5">
          <component :is="rankIcon(user.rank)" class="w-4 h-4 text-neon-green" stroke-width="2.4" />
          <span class="text-neon-green text-xs font-bold tracking-wider">{{ user.rank }}</span>
        </div>
      </div>

      <ProgressBar :current-xp="user.currentXp" :total-xp="user.xpForNext" />
    </div>

    <ExerciseCard
      v-for="ex in exercises"
      :key="ex.id"
      :exercise-name="ex.name"
    />

    <button
      class="w-full py-4 rounded-2xl bg-neon-green text-dark-bg font-bold text-sm
             tracking-widest uppercase active:scale-95 transition-transform
             shadow-[0_0_24px_rgba(34,255,119,0.25)]"
      @click="addExercise"
    >
      + Übung
    </button>

  </div>
</template>
