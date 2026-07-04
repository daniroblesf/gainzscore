<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { Dumbbell, Flame, LogOut, Trash2, Trophy, X } from '@lucide/vue'
import { API_BASE_URL, clearSession, getStoredUser } from '../utils/api'

const router = useRouter()
const storedUser = getStoredUser()
const username = storedUser?.name || 'GainzPlayer'
const totalWorkouts = ref(0)
const totalXp = ref(storedUser?.current_xp || 0)
const accountMessage = ref('')
const deletePassword = ref('')
const isDeletingAccount = ref(false)
const isDeleteOpen = ref(false)

onMounted(() => {
  loadHomeStats()
})

async function loadHomeStats() {
  try {
    const response = await fetch(`${API_BASE_URL}/home-stats`)

    if (!response.ok) return

    const data = await response.json()
    totalWorkouts.value = data.total_workouts
    totalXp.value = data.total_xp
  } catch {
    totalWorkouts.value = 0
  }
}

function logout() {
  clearSession()
  router.push('/login')
}

function openDeleteAccount() {
  accountMessage.value = ''
  deletePassword.value = ''
  isDeleteOpen.value = true
}

function closeDeleteAccount() {
  isDeleteOpen.value = false
  deletePassword.value = ''
}

async function deleteAccount() {
  accountMessage.value = ''

  if (!storedUser?.id) {
    accountMessage.value = 'Kein eingeloggter User gefunden.'
    return
  }

  if (!deletePassword.value) {
    accountMessage.value = 'Bitte Passwort eingeben.'
    return
  }

  isDeletingAccount.value = true

  try {
    const response = await fetch(`${API_BASE_URL}/users/${storedUser.id}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify({ password: deletePassword.value }),
    })

    if (response.status === 401) {
      accountMessage.value = 'Passwort ist falsch.'
      return
    }

    if (!response.ok) {
      accountMessage.value = 'Konto konnte nicht geloescht werden.'
      return
    }

    clearSession()
    router.push('/login')
  } catch {
    accountMessage.value = 'Backend ist nicht erreichbar.'
  } finally {
    isDeletingAccount.value = false
  }
}
</script>

<template>
  <div class="min-h-screen w-full">
    <div class="page-shell space-y-6 text-white">
      <header class="flex items-center justify-between pt-4">
        <div>
          <p class="text-[10px] tracking-widest text-white/30 uppercase">Welcome back</p>
          <h1 class="text-xl font-bold leading-none">
            <span class="text-neon-green">HALLO </span>
            <span class="text-white">{{ username }}</span>
          </h1>
        </div>

        <div class="flex items-center gap-3">
          <h2 class="text-xl font-bold leading-none">
            <span class="text-neon-green">GAINZ</span><span class="text-white">SCORE</span>
          </h2>

          <button
            class="w-9 h-9 rounded-xl border border-white/10 bg-black/35 text-white/45
                   flex items-center justify-center hover:text-neon-green hover:border-neon-green/50
                   transition-colors active:scale-95"
            title="Logout"
            @click="logout"
          >
            <LogOut class="w-4 h-4" stroke-width="2.4" />
          </button>
        </div>
      </header>

      <div class="grid grid-cols-2 gap-4">
        <div class="card flex flex-col justify-between h-28">
          <div>
            <Dumbbell class="w-5 h-5 text-neon-green" stroke-width="2.4" />
            <p class="text-[10px] font-bold tracking-widest text-white/40 uppercase mt-2">Workouts</p>
          </div>
          <p class="text-2xl font-extrabold text-white tabular-nums">{{ totalWorkouts }}</p>
        </div>

        <div class="card flex flex-col justify-between h-28">
          <div>
            <Flame class="w-5 h-5 text-neon-green" stroke-width="2.4" />
            <p class="text-[10px] font-bold tracking-widest text-white/40 uppercase mt-2">Total XP</p>
          </div>
          <p class="text-2xl font-extrabold text-neon-green tabular-nums">{{ totalXp.toLocaleString() }}</p>
        </div>
      </div>

      <div class="space-y-3 pt-2">
        <router-link
          to="/tracker"
          class="primary-action flex items-center justify-center gap-2"
        >
          <Dumbbell class="w-5 h-5" stroke-width="2.4" />
          <span>Start Workout</span>
        </router-link>

        <router-link
          to="/ranking"
          class="secondary-action flex items-center justify-center gap-2"
        >
          <Trophy class="w-5 h-5" stroke-width="2.4" />
          <span>View Leaderboard</span>
        </router-link>
      </div>

      <div class="card space-y-3 border-red-400/20">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-[10px] tracking-widest uppercase text-red-300/70">Account</p>
            <p class="text-sm font-bold text-white">Konto verwalten</p>
          </div>

          <button
            v-if="isDeleteOpen"
            class="w-8 h-8 rounded-lg border border-white/10 text-white/45 flex items-center justify-center
                   hover:text-white hover:border-white/30 transition-colors"
            @click="closeDeleteAccount"
          >
            <X class="w-4 h-4" stroke-width="2.4" />
          </button>
        </div>

        <button
          v-if="!isDeleteOpen"
          class="w-full py-3 rounded-xl border border-red-400/30 bg-red-500/10
                 text-red-200 font-bold text-xs tracking-widest uppercase
                 flex items-center justify-center gap-2 hover:bg-red-500/20
                 transition-colors disabled:opacity-50"
          @click="openDeleteAccount"
        >
          <Trash2 class="w-4 h-4" stroke-width="2.4" />
          <span>Konto loeschen</span>
        </button>

        <form v-else class="space-y-3" @submit.prevent="deleteAccount">
          <p class="text-[10px] leading-relaxed tracking-widest uppercase text-white/35">
            Loeschen entfernt dein Konto, Workouts und Leistungen dauerhaft.
          </p>

          <input
            v-model="deletePassword"
            type="password"
            autocomplete="current-password"
            placeholder="Passwort bestaetigen"
            class="input-field"
          />

          <button
            class="w-full py-3 rounded-xl border border-red-400/30 bg-red-500/10
                   text-red-200 font-bold text-xs tracking-widest uppercase
                   flex items-center justify-center gap-2 hover:bg-red-500/20
                   transition-colors disabled:opacity-50"
            :disabled="isDeletingAccount"
            type="submit"
          >
            <Trash2 class="w-4 h-4" stroke-width="2.4" />
            <span>{{ isDeletingAccount ? 'Loescht' : 'Endgueltig loeschen' }}</span>
          </button>
        </form>

        <p
          v-if="accountMessage"
          class="text-center text-[10px] tracking-widest uppercase text-red-300"
        >
          {{ accountMessage }}
        </p>
      </div>
    </div>
  </div>
</template>
