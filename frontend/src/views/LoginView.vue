<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { API_BASE_URL, storeSession } from '../utils/api'

const router = useRouter()
const mode = ref('login')
const name = ref('')
const email = ref('')
const password = ref('')
const errorMessage = ref('')
const isLoading = ref(false)

const isRegisterMode = computed(() => mode.value === 'register')
const formTitle = computed(() => isRegisterMode.value ? 'Create your account' : 'Sign in to continue')
const submitLabel = computed(() => {
  if (isLoading.value) return 'Bitte warten'
  return isRegisterMode.value ? 'Registrieren' : 'Login'
})

function switchMode(nextMode) {
  mode.value = nextMode
  errorMessage.value = ''
}

async function handleSubmit() {
  errorMessage.value = ''
  isLoading.value = true

  const endpoint = isRegisterMode.value ? 'register' : 'login'
  const payload = isRegisterMode.value
    ? {
        name: name.value,
        email: email.value,
        password: password.value,
      }
    : {
        email: email.value,
        password: password.value,
      }

  try {
    const response = await fetch(`${API_BASE_URL}/${endpoint}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify(payload),
    })

    if (response.status === 401) {
      errorMessage.value = 'Email oder Passwort ist falsch.'
      return
    }

    if (response.status === 422) {
      const data = await response.json()
      const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null
      errorMessage.value = firstError || 'Bitte pruefe deine Eingaben.'
      return
    }

    if (!response.ok) {
      errorMessage.value = isRegisterMode.value
        ? 'Registrierung fehlgeschlagen.'
        : 'Serverfehler beim Login.'
      return
    }

    const session = await response.json()
    storeSession(session)
    router.push('/home')
  } catch {
    errorMessage.value = 'Backend ist nicht erreichbar.'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen w-full flex items-center justify-center">
    <div class="w-full max-w-md mx-auto px-4 py-10 space-y-8 text-white">
      <div class="text-center space-y-1">
        <h1 class="text-3xl font-bold leading-none">
          <span class="text-neon-green">GAINZ</span><span class="text-white">SCORE</span>
        </h1>
        <p class="text-[10px] tracking-widest text-white/30 uppercase">Level up your gym life</p>
      </div>

      <div class="card p-6 space-y-5">
        <div class="grid grid-cols-2 gap-2 rounded-xl bg-white/5 p-1 border border-white/10">
          <button
            type="button"
            class="py-2 rounded-lg text-xs font-bold tracking-widest uppercase transition-colors"
            :class="!isRegisterMode ? 'bg-neon-green text-dark-bg' : 'text-white/40 hover:text-white'"
            @click="switchMode('login')"
          >
            Login
          </button>
          <button
            type="button"
            class="py-2 rounded-lg text-xs font-bold tracking-widest uppercase transition-colors"
            :class="isRegisterMode ? 'bg-neon-green text-dark-bg' : 'text-white/40 hover:text-white'"
            @click="switchMode('register')"
          >
            Register
          </button>
        </div>

        <div class="space-y-1">
          <p class="text-[10px] tracking-widest text-white/30 uppercase">
            {{ isRegisterMode ? 'New player' : 'Welcome back' }}
          </p>
          <h2 class="text-lg font-bold leading-none text-white">
            {{ formTitle }}
          </h2>
        </div>

        <form class="space-y-4" @submit.prevent="handleSubmit">
          <div v-if="isRegisterMode" class="space-y-1.5">
            <label
              for="name"
              class="block text-[10px] tracking-widest text-white/40 uppercase font-bold"
            >
              Name
            </label>
            <input
              id="name"
              v-model="name"
              type="text"
              autocomplete="name"
              required
              minlength="2"
              maxlength="30"
              placeholder="GainzPlayer"
              class="input-field"
            />
          </div>

          <div class="space-y-1.5">
            <label
              for="email"
              class="block text-[10px] tracking-widest text-white/40 uppercase font-bold"
            >
              Email
            </label>
            <input
              id="email"
              v-model="email"
              type="email"
              autocomplete="email"
              required
              placeholder="demo@gainzscore.app"
              class="input-field"
            />
          </div>

          <div class="space-y-1.5">
            <label
              for="password"
              class="block text-[10px] tracking-widest text-white/40 uppercase font-bold"
            >
              Password
            </label>
            <input
              id="password"
              v-model="password"
              type="password"
              :autocomplete="isRegisterMode ? 'new-password' : 'current-password'"
              required
              minlength="6"
              placeholder="password"
              class="input-field"
            />
          </div>

          <button
            type="submit"
            :disabled="isLoading"
            class="primary-action"
          >
            {{ submitLabel }}
          </button>

          <p
            v-if="errorMessage"
            class="text-center text-[10px] tracking-widest uppercase text-red-300"
          >
            {{ errorMessage }}
          </p>
        </form>
      </div>
    </div>
  </div>
</template>
