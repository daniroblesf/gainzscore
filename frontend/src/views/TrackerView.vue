<script setup>
import { computed, onMounted, ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import {
  Gem,
  Shield,
  Trophy,
  Medal,
  Dumbbell,
  ClipboardCheck,
  BarChart3,
  LogOut,
} from '@lucide/vue'

import ProgressBar from '../components/ProgressBar.vue'
import ExerciseCard from '../components/ExerciseCard.vue'
import ExerciseSelectorModal from '../components/ExerciseSelectorModal.vue'
import { API_BASE_URL, clearSession, getStoredUser, storeSession } from '../utils/api'
import { getExerciseImage } from '../utils/imageUrl'

const router = useRouter()
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
const totalSets = computed(() => exercises.value.reduce((sum, exercise) => sum + exercise.sets.length, 0))
const completedSets = computed(() => exercises.value.reduce((sum, exercise) => {
  return sum + exercise.sets.filter(set => set.completed).length
}, 0))
const workoutVolume = computed(() => exercises.value.reduce((sum, exercise) => {
  return sum + exercise.sets.reduce((setSum, set) => {
    const kg = Number(set.kg)
    const reps = Number(set.reps)
    if (!Number.isFinite(kg) || !Number.isFinite(reps)) return setSum
    return setSum + kg * reps
  }, 0)
}, 0))
const KG_MIN = 1
const KG_MAX = 500
const REPS_MIN = 1
const REPS_MAX = 100

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

function logout() {
  clearSession()
  router.push('/login')
}

async function loadWorkoutStatus() {
  try {
    const response = await fetch(`${API_BASE_URL}/users/${user.value.id}/workouts`, {
  headers: {
    Accept: 'application/json',
    Authorization: `Bearer ${localStorage.getItem('gainzscore_token')}`,
  },
})

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
    image: getExerciseImage(exercise),
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

function isValidWorkoutSet(set) {
  const kg = Number(set.kg)
  const reps = Number(set.reps)

  return set.completed
    && Number.isFinite(kg)
    && Number.isFinite(reps)
    && kg >= KG_MIN
    && kg <= KG_MAX
    && reps >= REPS_MIN
    && reps <= REPS_MAX
}

function workoutValidationMessage() {
  for (const exercise of exercises.value) {
    for (const set of exercise.sets) {
      if (!isValidWorkoutSet(set)) {
        return `Bitte alle Saetze bei ${exercise.name} mit echten Werten abschliessen: KG ${KG_MIN}-${KG_MAX}, Wdh. ${REPS_MIN}-${REPS_MAX}.`
      }
    }
  }

  return ''
}

async function finishWorkout() {
  if (exercises.value.length === 0) {
    alert('Bitte fuege zuerst mindestens eine Uebung hinzu.')
    return
  }

  const validationMessage = workoutValidationMessage()
  if (validationMessage) {
    saveMessage.value = validationMessage
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
    Authorization: `Bearer ${localStorage.getItem('gainzscore_token')}`,
  },
  body: JSON.stringify(payload),
})
    if (!response.ok) {
      throw new Error('Workout konnte nicht gespeichert werden.')
    }

    const result = await response.json()

    if (result.xp) {
      user.value = {
        ...user.value,
        rank: result.xp.rank,
        level: result.xp.level,
        currentXp: result.xp.current_xp,
        xpForNext: result.xp.xp_for_next,
      }

      storeSession({
        token: localStorage.getItem('gainzscore_token') || '',
        user: {
          id: user.value.id,
          name: user.value.name,
          rank: user.value.rank,
          level: user.value.level,
          current_xp: user.value.currentXp,
          xp_for_next: user.value.xpForNext,
        },
      })
    }

    exercises.value = []
    hasSavedWorkouts.value = true
    saveMessage.value = `Training gespeichert +${result.xp?.xp_gained || 0} XP`
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
          <button
            class="w-9 h-9 rounded-xl border border-white/10 bg-black/35 text-white/45
                   flex items-center justify-center hover:text-neon-green hover:border-neon-green/50
                   transition-colors active:scale-95"
            title="Logout"
            @click="logout"
          >
            <LogOut class="w-4 h-4" stroke-width="2.4" />
          </button>

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

    <div v-if="exercises.length > 0" class="card grid grid-cols-3 gap-2 text-center">
      <div>
        <p class="text-[9px] tracking-widest uppercase text-white/30">Uebungen</p>
        <p class="text-lg font-extrabold text-white tabular-nums">{{ exercises.length }}</p>
      </div>
      <div>
        <p class="text-[9px] tracking-widest uppercase text-white/30">Saetze</p>
        <p class="text-lg font-extrabold text-neon-green tabular-nums">{{ completedSets }}/{{ totalSets }}</p>
      </div>
      <div>
        <p class="text-[9px] tracking-widest uppercase text-white/30">Volumen</p>
        <p class="text-lg font-extrabold text-white tabular-nums">{{ workoutVolume.toLocaleString() }}</p>
      </div>
    </div>

    <div class="space-y-3 pt-4">
      <button
        class="primary-action"
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
        class="secondary-action flex items-center justify-center gap-2"
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
        :class="saveMessage.startsWith('Training gespeichert') ? 'text-neon-green/70' : 'text-red-300'"
      >
        {{ saveMessage }}
      </p>
    </div>
  </div>

  <ExerciseSelectorModal
    v-if="isModalOpen"
    :user-id="user.id"
    @select="addExerciseToWorkout"
    @close="isModalOpen = false"
  />
</template>
