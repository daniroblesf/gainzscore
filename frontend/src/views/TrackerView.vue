<script setup>
import { computed, onMounted, ref } from 'vue'
import { RouterLink } from 'vue-router'
import {
  Gem,
  Shield,
  Trophy,
  Medal,
  Dumbbell,
  ClipboardCheck,
  BarChart3,
} from '@lucide/vue'

import ProgressBar from '../components/ProgressBar.vue'
import ExerciseCard from '../components/ExerciseCard.vue'
import ExerciseSelectorModal from '../components/ExerciseSelectorModal.vue'
import { API_BASE_URL, getStoredUser } from '../utils/api'

const today = new Date().toISOString().slice(0, 10)
const dayName = new Date().toLocaleDateString('de-DE', { weekday: 'long' })
const storedUser = getStoredUser()

const user = ref(storedUser
  ? {
      id: storedUser.id,
      name: storedUser.name,
      rank: storedUser.rank,
      level: storedUser.level,
      currentXp: storedUser.current_xp,
      xpForNext: storedUser.xp_for_next,
    }
  : {
      id: 1,
      name: 'GainzPlayer',
      rank: 'SILVER I',
      level: 3,
      currentXp: 650,
      xpForNext: 1000,
    })

const exercises = ref([])
const isModalOpen = ref(false)
const hasSavedWorkouts = ref(false)
const isSavingWorkout = ref(false)
const saveMessage = ref('')

const canFinishWorkout = computed(() => exercises.value.length > 0 && !isSavingWorkout.value)

onMounted(() => {
  loadWorkoutStatus()
})

function rankIcon(rankName) {
  if (rankName.startsWith('DIAMOND')) return Gem
  if (rankName.startsWith('PLATINUM')) return Shield
  if (rankName.startsWith('GOLD')) return Trophy
  if (rankName.startsWith('SILVER')) return Medal
  return Dumbbell
}

async function loadWorkoutStatus() {
  try {
    const response = await fetch(`${API_BASE_URL}/users/${user.value.id}/workouts`)

    if (!response.ok) {
      throw new Error('Workouts konnten nicht geladen werden.')
    }

    const data = await response.json()
    hasSavedWorkouts.value = data.workouts.length > 0
  } catch {
    hasSavedWorkouts.value = false
  }
}

function addExerciseToWorkout(exercise) {
  exercises.value.push({
    id: Date.now(),
    exerciseId: exercise.id,
    name: exercise.name,
    image: exercise.image,
    sets: [
      {
        set_number: 1,
        kg: 0,
        reps: 0,
        completed: false,
      },
    ],
  })

  saveMessage.value = ''
  isModalOpen.value = false
}

async function finishWorkout() {
  if (exercises.value.length === 0) {
    alert('Bitte fuege zuerst mindestens eine Uebung hinzu.')
    return
  }

  isSavingWorkout.value = true
  saveMessage.value = ''

  const payload = {
    user_id: user.value.id,
    date: today,
    exercises: exercises.value.map(exercise => ({
      exercise_id: exercise.exerciseId,
      sets: exercise.sets.map((set, index) => ({
        set_number: set.set_number || index + 1,
        kg: Number(set.kg),
        reps: Number(set.reps),
        completed: Boolean(set.completed),
      })),
    })),
  }

  try {
    const response = await fetch(`${API_BASE_URL}/workouts/finish`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify(payload),
    })

    if (!response.ok) {
      throw new Error('Workout konnte nicht gespeichert werden.')
    }

    exercises.value = []
    hasSavedWorkouts.value = true
    saveMessage.value = 'Training gespeichert'
  } catch {
    saveMessage.value = 'Training konnte nicht gespeichert werden.'
  } finally {
    isSavingWorkout.value = false
  }
}

function updateExerciseSets(index, updatedSets) {
  exercises.value[index].sets = updatedSets
}
</script>

<template>
  <div class="page-shell">
    <header class="space-y-3">
      <RouterLink
        to="/home"
        class="inline-flex items-center gap-1 text-[10px] tracking-widest text-white/40
               uppercase hover:text-neon-green transition-colors"
      >
        &larr; Home
      </RouterLink>

      <div class="flex items-center justify-between">
        <div>
          <p class="text-[10px] tracking-widest text-white/30 uppercase">
            Training
          </p>

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
            <p class="text-neon-green text-sm font-bold tabular-nums">
              {{ today }}
            </p>
            <p class="text-white/30 text-[10px] capitalize">
              {{ dayName }}
            </p>
          </div>
        </div>
      </div>
    </header>

    <div class="card">
      <div class="flex items-center justify-between mb-3">
        <div>
          <p class="text-[10px] tracking-widest text-white/30 uppercase mb-0.5">
            Level {{ user.level }}
          </p>

          <p class="text-sm font-bold text-white">
            {{ user.name }}
          </p>
        </div>

        <div class="flex items-center gap-1.5 bg-neon-green/10 border border-neon-green/30 rounded-lg px-3 py-1.5">
          <component
            :is="rankIcon(user.rank)"
            class="w-4 h-4 text-neon-green"
            stroke-width="2.4"
          />

          <span class="text-neon-green text-xs font-bold tracking-wider">
            {{ user.rank }}
          </span>
        </div>
      </div>

      <ProgressBar :current-xp="user.currentXp" :total-xp="user.xpForNext" />
    </div>

    <div
      v-if="exercises.length === 0"
      class="py-12 flex flex-col items-center gap-2 text-center"
    >
      <p class="text-[10px] tracking-widest uppercase text-white/20">
        Noch keine Uebungen
      </p>
      <p class="text-[10px] tracking-widest uppercase text-white/10">
        Tippe auf + Uebung
      </p>
    </div>

    <ExerciseCard
      v-for="(ex, i) in exercises"
      :key="ex.id"
      :exercise-name="ex.name"
      :exercise-image="ex.image"
      :sets="ex.sets"
      @update-sets="updateExerciseSets(i, $event)"
    />

    <div class="space-y-3 pt-4">
      <button
        class="w-full py-4 rounded-2xl bg-neon-green text-dark-bg font-bold text-sm
               tracking-widest uppercase active:scale-95 transition-transform
               shadow-[0_0_24px_rgba(34,255,119,0.25)]"
        @click="isModalOpen = true"
      >
        + Uebung
      </button>

      <button
        class="w-full py-4 rounded-2xl font-bold text-sm tracking-widest uppercase
               active:scale-95 transition-transform flex items-center justify-center gap-2"
        :class="!canFinishWorkout
          ? 'bg-white/20 text-white/30 cursor-not-allowed'
          : 'bg-white text-dark-bg hover:bg-white/90'"
        :disabled="!canFinishWorkout"
        @click="finishWorkout"
      >
        <ClipboardCheck class="w-5 h-5" stroke-width="2.4" />
        <span>{{ isSavingWorkout ? 'Speichert' : 'Training beenden' }}</span>
      </button>

      <RouterLink
        v-if="hasSavedWorkouts"
        to="/results"
        class="w-full py-4 rounded-2xl bg-black/50 backdrop-blur-md border border-neon-green/40
               text-neon-green font-bold text-center text-sm tracking-widest uppercase
               active:scale-95 transition-all hover:bg-neon-green hover:text-dark-bg
               shadow-[0_0_18px_rgba(34,255,119,0.12)]
               flex items-center justify-center gap-2"
      >
        <BarChart3 class="w-5 h-5" stroke-width="2.4" />
        <span>Leistungen ansehen</span>
      </RouterLink>

      <button
        v-else
        class="w-full py-4 rounded-2xl bg-black/30 border border-white/10
               text-white/25 font-bold text-center text-sm tracking-widest uppercase
               cursor-not-allowed flex items-center justify-center gap-2"
        disabled
      >
        <BarChart3 class="w-5 h-5" stroke-width="2.4" />
        <span>Leistungen ansehen</span>
      </button>

      <p
        v-if="saveMessage"
        class="text-center text-[10px] tracking-widest uppercase"
        :class="hasSavedWorkouts ? 'text-neon-green/70' : 'text-red-300'"
      >
        {{ saveMessage }}
      </p>
    </div>
  </div>

  <ExerciseSelectorModal
    v-if="isModalOpen"
    @select="addExerciseToWorkout"
    @close="isModalOpen = false"
  />
</template>
