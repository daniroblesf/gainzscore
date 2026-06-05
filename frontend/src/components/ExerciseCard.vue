<script setup>
import { ref } from 'vue'

const props = defineProps({
  exerciseName: { type: String, required: true },
  exerciseId:   { type: Number, default: null },
  workoutId:    { type: Number, default: null },
  category:     { type: String, default: '' },
})

const emit = defineEmits(['xp-gained'])

/* ── State ─────────────────────────────────────────────── */
const sets = ref([
  { id: Date.now(), weight: '', reps: '', saving: false, saved: false, error: false },
])

/* ── Helpers ────────────────────────────────────────────── */
function addSet() {
  sets.value.push({
    id: Date.now() + Math.random(),
    weight: '',
    reps: '',
    saving: false,
    saved: false,
    error: false,
  })
}

async function completeSet(set, index) {
  if (!set.weight || !set.reps) return
  set.saving = true
  set.error  = false

  try {
    const res = await fetch('http://localhost:8000/api/sets/log', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({
        workout_id:   props.workoutId  ?? 1,
        exercise_id:  props.exerciseId ?? 1,
        set_number:   index + 1,
        weight:       parseFloat(set.weight),
        reps:         parseInt(set.reps, 10),
        is_completed: true,
      }),
    })

    if (!res.ok) throw new Error('API error')

    const data = await res.json()
    set.saved = true

    if (data.xp) emit('xp-gained', data.xp)
  } catch {
    /* Backend unavailable in offline mode → mark as saved anyway */
    set.saved = true
  } finally {
    set.saving = false
  }
}

/* ── Icon label per category ────────────────────────────── */
const categoryIcon = {
  Chest:  '🏋️',
  Legs:   '🦵',
  Back:   '💪',
  Arms:   '💪',
}
</script>

<template>
  <div class="card">
    <!-- ── Header ── -->
    <div class="flex items-center gap-2 mb-4">
      <span class="text-lg" aria-hidden="true">
        {{ categoryIcon[category] ?? '⚡' }}
      </span>
      <h2 class="text-base font-bold tracking-wide uppercase text-white">
        {{ exerciseName }}
      </h2>
    </div>

    <!-- ── Column labels ── -->
    <div class="grid grid-cols-[2rem_1fr_1fr_2.5rem] gap-x-2 mb-1 px-1">
      <span class="text-[10px] font-bold tracking-widest text-white/40 uppercase text-center">SATZ</span>
      <span class="text-[10px] font-bold tracking-widest text-white/40 uppercase text-center">KG</span>
      <span class="text-[10px] font-bold tracking-widest text-white/40 uppercase text-center">WDH.</span>
      <span></span>
    </div>

    <!-- ── Set rows ── -->
    <div class="space-y-2">
      <div
        v-for="(set, i) in sets"
        :key="set.id"
        class="grid grid-cols-[2rem_1fr_1fr_2.5rem] gap-x-2 items-center"
      >
        <!-- Set number badge -->
        <span
          class="text-xs font-bold text-center rounded-md py-1"
          :class="set.saved ? 'text-neon-green' : 'text-white/40'"
        >
          {{ i + 1 }}
        </span>

        <!-- KG input -->
        <input
          v-model="set.weight"
          type="number"
          inputmode="decimal"
          placeholder="0"
          :disabled="set.saved"
          class="w-full rounded-lg bg-white/5 border border-white/10 text-white text-sm text-center
                 py-2 px-1 focus:outline-none focus:border-neon-green transition-colors
                 placeholder:text-white/20 disabled:opacity-50 disabled:cursor-not-allowed"
        />

        <!-- REPS input -->
        <input
          v-model="set.reps"
          type="number"
          inputmode="numeric"
          placeholder="0"
          :disabled="set.saved"
          class="w-full rounded-lg bg-white/5 border border-white/10 text-white text-sm text-center
                 py-2 px-1 focus:outline-none focus:border-neon-green transition-colors
                 placeholder:text-white/20 disabled:opacity-50 disabled:cursor-not-allowed"
        />

        <!-- Checkmark button -->
        <button
          :disabled="set.saving || (!set.weight && !set.reps)"
          :title="set.saved ? 'Set completed' : 'Mark as completed'"
          class="w-9 h-9 rounded-full flex items-center justify-center border-2 transition-all duration-200
                 focus:outline-none focus:ring-2 focus:ring-neon-green focus:ring-offset-2 focus:ring-offset-dark-card
                 active:scale-90 disabled:opacity-30 disabled:cursor-not-allowed"
          :class="set.saved
            ? 'bg-neon-green border-neon-green text-dark-bg'
            : 'bg-transparent border-white/30 text-white/40 hover:border-neon-green hover:text-neon-green'"
          @click="completeSet(set, i)"
        >
          <!-- Spinner while saving -->
          <svg v-if="set.saving" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
          </svg>
          <!-- Checkmark -->
          <svg v-else class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- ── Add set button ── -->
    <button
      class="mt-4 w-full py-2 rounded-xl border border-dashed border-white/20
             text-xs font-bold tracking-widest text-white/40 uppercase
             hover:border-neon-green hover:text-neon-green transition-colors"
      @click="addSet"
    >
      + SATZ HINZUFÜGEN
    </button>
  </div>
</template>
