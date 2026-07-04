<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { API_BASE_URL, storeSession } from '../utils/api'

const router = useRouter()
const email = ref('')
const password = ref('')
const errorMessage = ref('')
const isLoading = ref(false)

async function handleLogin() {
  errorMessage.value = ''
  isLoading.value = true

  try {
    const response = await fetch(`${API_BASE_URL}/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify({
        email: email.value,
        password: password.value,
      }),
    })

    if (response.status === 401) {
      errorMessage.value = 'Email oder Passwort ist falsch.'
      return
    }

    if (!response.ok) {
      errorMessage.value = 'Serverfehler beim Login.'
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
        <div class="space-y-1">
          <p class="text-[10px] tracking-widest text-white/30 uppercase">Welcome back</p>
          <h2 class="text-lg font-bold leading-none text-white">Sign in to continue</h2>
        </div>

        <form class="space-y-4" @submit.prevent="handleLogin">
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
              autocomplete="current-password"
              required
              placeholder="password"
              class="input-field"
            />
          </div>

          <button
            type="submit"
            :disabled="isLoading"
            class="primary-action"
          >
            {{ isLoading ? 'Bitte warten' : 'Login' }}
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
