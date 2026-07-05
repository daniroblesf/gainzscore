<script setup>
import { computed, onMounted, ref } from 'vue'
import { RouterLink } from 'vue-router'
import { BarChart3, CalendarDays, Dumbbell } from '@lucide/vue'

import { API_BASE_URL, getStoredUser } from '../utils/api'
import { getExerciseImage } from '../utils/imageUrl'

const demoUsers = [
  { id: 1, name: 'GainzPlayer', rank: 'SILVER I', total_xp: 3650 },
  { id: 2, name: 'IronBeast', rank: 'DIAMOND', total_xp: 29800 },
  { id: 3, name: 'GoldGunther', rank: 'PLATINUM I', total_xp: 17100 },
  { id: 4, name: 'SilverStreak', rank: 'GOLD II', total_xp: 10400 },
  { id: 5, name: 'BronzeBull', rank: 'BRONZE III', total_xp: 1300 },
]

const storedUser = getStoredUser()
const selectedUser = ref(storedUser || demoUsers[0])
const users = ref([])
const workouts = ref([])
const isLoadingUsers = ref(true)
const isLoading = ref(true)
const errorMessage = ref('')

const totalWorkouts = computed(() => workouts.value.length)
const totalSets = computed(() => workouts.value.reduce((sum, workout) => {
  return sum + workout.exercises.reduce((exerciseSum, exercise) => {
    return exerciseSum + exercise.sets.length
  }, 0)
}, 0))
const totalReps = computed(() => workouts.value.reduce((sum, workout) => {
  return sum + workout.exercises.reduce((exerciseSum, exercise) => {
    return exerciseSum + exercise.sets.reduce((setSum, set) => setSum + Number(set.reps), 0)
  }, 0)
}, 0))

onMounted(() => {
  loadUsers()
  loadWorkouts(selectedUser.value.id)
})

async function loadUsers() {
  isLoadingUsers.value = true

  try {
    const response = await fetch(`${API_BASE_URL}/ranking`, {
  headers: {
    Accept: 'application/json',
    Authorization: `Bearer ${localStorage.getItem('gainzscore_token')}`,
  },
})

    if (!response.ok) {
      throw new Error('Benutzer konnten nicht geladen werden.')
    }

    const data = await response.json()
    users.value = data.data
  } catch {
    users.value = demoUsers
  } finally {
    isLoadingUsers.value = false
  }
}

function selectUser(user) {
  selectedUser.value = user
  loadWorkouts(user.id)
}

async function loadWorkouts(userId = selectedUser.value.id) {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const response = await fetch(`${API_BASE_URL}/users/${userId}/workouts`, {
  headers: {
    Accept: 'application/json',
    Authorization: `Bearer ${localStorage.getItem('gainzscore_token')}`,
  },
})

    if (!response.ok) {
      throw new Error('Leistungen konnten nicht geladen werden.')
    }

    const data = await response.json()
    workouts.value = data.workouts
  } catch {
    errorMessage.value = 'Leistungen konnten nicht geladen werden.'
  } finally {
    isLoading.value = false
  }
}

function formatDate(date) {
  return new Intl.DateTimeFormat('de-DE', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  }).format(new Date(date))
}
</script>

<template>
  <div class="page-shell">
    <header class="space-y-3">
      <RouterLink
        to="/tracker"
        class="inline-flex items-center gap-1 text-[10px] tracking-widest text-white/40
               uppercase hover:text-neon-green transition-colors"
      >
        &larr; Training
      </RouterLink>

      <div class="flex items-center justify-between">
        <div>
          <p class="text-[10px] tracking-widest text-white/30 uppercase">
            Verlauf
          </p>
          <h1 class="text-xl font-bold leading-none">
            <span class="text-neon-green">LEIST</span><span class="text-white">UNGEN</span>
          </h1>
        </div>

        <div class="w-10 h-10 rounded-xl bg-neon-green/10 border border-neon-green/30 flex items-center justify-center">
          <BarChart3 class="w-5 h-5 text-neon-green" stroke-width="2.4" />
        </div>
      </div>
    </header>

    <section class="space-y-2">
      <div class="flex items-center justify-between px-1">
        <p class="text-[10px] tracking-widest uppercase text-white/30">
          Benutzer
        </p>
        <p
          v-if="isLoadingUsers"
          class="text-[10px] tracking-widest uppercase text-white/20"
        >
          Laedt
        </p>
      </div>

      <div class="grid grid-cols-1 gap-2">
        <button
          v-for="userOption in users"
          :key="userOption.id"
          class="w-full grid grid-cols-[1fr_auto] gap-3 items-center text-left rounded-xl border px-4 py-3
                 transition-colors active:scale-[0.99]"
          :class="selectedUser.id === userOption.id
            ? 'bg-neon-green text-dark-bg border-neon-green'
            : 'bg-dark-card text-white border-white/10 hover:border-neon-green/50'"
          @click="selectUser(userOption)"
        >
          <span class="min-w-0">
            <span class="block text-sm font-bold truncate">
              {{ userOption.name }}
            </span>
            <span
              class="block text-[10px] tracking-widest uppercase"
              :class="selectedUser.id === userOption.id ? 'text-dark-bg/60' : 'text-white/30'"
            >
              {{ userOption.rank }}
            </span>
          </span>

          <span
            class="text-xs font-bold tabular-nums"
            :class="selectedUser.id === userOption.id ? 'text-dark-bg' : 'text-neon-green'"
          >
            {{ Number(userOption.total_xp || 0).toLocaleString() }} XP
          </span>
        </button>
      </div>
    </section>

    <div class="flex items-center justify-between px-1">
      <p class="text-[10px] tracking-widest uppercase text-white/30">
        Leistungen von
      </p>
      <p class="text-[10px] tracking-widest uppercase text-neon-green">
        {{ selectedUser.name }}
      </p>
    </div>

    <div v-if="isLoading" class="py-16 text-center">
      <p class="text-[10px] tracking-widest uppercase text-white/25">
        Lade Leistungen
      </p>
    </div>

    <div v-else-if="errorMessage" class="card text-center space-y-3">
      <p class="text-xs tracking-widest uppercase text-red-300">
        {{ errorMessage }}
      </p>
      <button
        class="w-full py-3 rounded-xl bg-white text-dark-bg font-bold text-xs tracking-widest uppercase"
        @click="loadWorkouts"
      >
      Erneut laden
      </button>
    </div>

    <div v-else-if="workouts.length === 0" class="py-16 text-center space-y-3">
      <p class="text-[10px] tracking-widest uppercase text-white/25">
        Keine Leistungen vorhanden
      </p>
      <RouterLink
        to="/tracker"
        class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-neon-green
               text-dark-bg text-xs font-bold tracking-widest uppercase"
      >
        Training starten
      </RouterLink>
    </div>

    <template v-else>
      <div class="grid grid-cols-3 gap-2">
        <div class="card text-center">
          <p class="text-[9px] tracking-widest uppercase text-white/30">Workouts</p>
          <p class="text-xl font-extrabold text-white tabular-nums">{{ totalWorkouts }}</p>
        </div>
        <div class="card text-center">
          <p class="text-[9px] tracking-widest uppercase text-white/30">Saetze</p>
          <p class="text-xl font-extrabold text-neon-green tabular-nums">{{ totalSets }}</p>
        </div>
        <div class="card text-center">
          <p class="text-[9px] tracking-widest uppercase text-white/30">Wdh.</p>
          <p class="text-xl font-extrabold text-white tabular-nums">{{ totalReps }}</p>
        </div>
      </div>

      <section
        v-for="workout in workouts"
        :key="workout.id"
        class="card space-y-4"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <CalendarDays class="w-4 h-4 text-neon-green" stroke-width="2.4" />
            <p class="text-sm font-bold text-white">
              {{ formatDate(workout.date) }}
            </p>
          </div>

          <p class="text-[10px] tracking-widest uppercase text-white/30">
            {{ workout.exercises.length }} Uebungen
          </p>
        </div>

        <div class="space-y-3">
          <div
            v-for="exercise in workout.exercises"
            :key="exercise.exercise_id"
            class="border-t border-white/10 pt-3"
          >
            <div class="flex items-center gap-3 mb-2">
              <div class="w-9 h-9 rounded-full overflow-hidden bg-white/5 flex items-center justify-center border border-white/10">
                <img
                  :src="getExerciseImage({ name: exercise.name, image: exercise.image })"
                  :alt="exercise.name"
                  class="w-full h-full object-cover"
                  @error="(e) => e.target.style.display = 'none'"
                />
                <Dumbbell class="w-4 h-4 text-white/20" stroke-width="2.4" />
              </div>
              <p class="text-sm font-bold text-white truncate">
                {{ exercise.name }}
              </p>
            </div>

            <div class="grid grid-cols-[2rem_1fr_1fr] gap-x-2 px-1 mb-1">
              <span class="text-[9px] tracking-widest uppercase text-white/30 text-center">#</span>
              <span class="text-[9px] tracking-widest uppercase text-white/30 text-center">KG</span>
              <span class="text-[9px] tracking-widest uppercase text-white/30 text-center">Wdh.</span>
            </div>

            <div class="space-y-1">
              <div
                v-for="set in exercise.sets"
                :key="set.id"
                class="grid grid-cols-[2rem_1fr_1fr] gap-x-2 items-center rounded-lg bg-white/5 px-1 py-2"
              >
                <span class="text-xs font-bold text-center text-neon-green">
                  {{ set.set_number }}
                </span>
                <span class="text-sm text-center text-white tabular-nums">
                  {{ set.weight }}
                </span>
                <span class="text-sm text-center text-white tabular-nums">
                  {{ set.reps }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </section>
    </template>
  </div>
</template>
