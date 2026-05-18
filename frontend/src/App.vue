<script setup>
import { ref, computed } from 'vue'
import ProgressBar from './components/ProgressBar.vue'
import ExerciseCard from './components/ExerciseCard.vue'

/* ── Date header ─────────────────────────────────────────── */
const today = new Date().toISOString().slice(0, 10)   // "2026-05-18"
const dayName = new Date().toLocaleDateString('de-DE', { weekday: 'long' })

/* ── User XP state (reacts to completed sets) ────────────── */
const user = ref({
  name:       'GainzPlayer',
  rank:       'SILBER I',
  level:      3,
  currentXp:  650,
  xpForNext:  1000,
})

function onXpGained(xpResult) {
  user.value.currentXp = xpResult.current_xp
  user.value.xpForNext = xpResult.xp_for_next
  user.value.level     = xpResult.level
  user.value.rank      = xpResult.rank
}

/* ── Exercise list ───────────────────────────────────────── */
const exercises = ref([
  { id: 1, name: 'Bankdrücken',  category: 'Pecho'   },
  { id: 2, name: 'Latziehen',    category: 'Espalda' },
  { id: 3, name: 'Kniebeugen',   category: 'Pierna'  },
  { id: 4, name: 'Bizeps Curls', category: 'Brazo'   },
])

const workoutId = ref(1)

function addExercise() {
  const name = prompt('Übungsname eingeben:')
  if (name?.trim()) {
    exercises.value.push({
      id: Date.now(),
      name: name.trim(),
      category: 'Otro',
    })
  }
}
</script>

<template>
  <div class="bg-dark-bg min-h-screen text-white font-mono">
    <div class="max-w-md mx-auto px-4 py-6 space-y-4">

      <!-- ── App Header ──────────────────────────────── -->
      <header class="flex items-center justify-between">
        <div>
          <p class="text-[10px] tracking-widest text-white/30 uppercase">Training</p>
          <h1 class="text-xl font-bold leading-none">
            <span class="text-neon-green">GAINZ</span><span class="text-white">SCORE</span>
          </h1>
        </div>
        <div class="text-right">
          <p class="text-neon-green text-sm font-bold tabular-nums">{{ today }}</p>
          <p class="text-white/30 text-[10px] capitalize">{{ dayName }}</p>
        </div>
      </header>

      <!-- ── Level / XP card ────────────────────────── -->
      <div class="card">
        <div class="flex items-center justify-between mb-3">
          <div>
            <p class="text-[10px] tracking-widest text-white/30 uppercase mb-0.5">Level {{ user.level }}</p>
            <p class="text-sm font-bold text-white">{{ user.name }}</p>
          </div>
          <!-- Rank badge -->
          <div class="flex items-center gap-1.5 bg-neon-green/10 border border-neon-green/30 rounded-lg px-3 py-1.5">
            <span class="text-base">💎</span>
            <span class="text-neon-green text-xs font-bold tracking-wider">{{ user.rank }}</span>
          </div>
        </div>

        <ProgressBar
          :rank="user.rank"
          :current-xp="user.currentXp"
          :total-xp="user.xpForNext"
        />
      </div>

      <!-- ── Exercise cards ─────────────────────────── -->
      <ExerciseCard
        v-for="ex in exercises"
        :key="ex.id"
        :exercise-name="ex.name"
        :exercise-id="ex.id"
        :workout-id="workoutId"
        :category="ex.category"
        @xp-gained="onXpGained"
      />

      <!-- ── Add exercise CTA ───────────────────────── -->
      <button
        class="w-full py-4 rounded-2xl bg-neon-green text-dark-bg font-bold text-sm
               tracking-widest uppercase active:scale-95 transition-transform
               focus:outline-none focus:ring-2 focus:ring-neon-green focus:ring-offset-2
               focus:ring-offset-dark-bg shadow-[0_0_24px_rgba(34,255,119,0.25)]"
        @click="addExercise"
      >
        + Übung
      </button>

    </div>
  </div>
</template>
