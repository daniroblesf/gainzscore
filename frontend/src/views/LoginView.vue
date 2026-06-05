<script setup>
import { ref } from 'vue'

const email    = ref('')
const password = ref('')
const loading  = ref(false)
const error    = ref('')

async function handleLogin() {
  if (!email.value || !password.value) {
    error.value = 'Please enter your email and password.'
    return
  }

  loading.value = true
  error.value   = ''

  // TODO: Implement the POST /api/login request here using fetch or axios.
  //       Send the body as JSON: { email, password }
  //       On a 200 response, store the token in localStorage:
  //         localStorage.setItem('gainz_token', data.token)
  //         localStorage.setItem('gainz_user',  JSON.stringify(data.user))
  //       Then redirect to /tracker with: router.push('/tracker')
  //       On a 401 response, show: error.value = 'Invalid credentials.'
  //
  // Example with fetch:
  //   const res  = await fetch('http://localhost:8000/api/login', {
  //     method:  'POST',
  //     headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
  //     body:    JSON.stringify({ email: email.value, password: password.value }),
  //   })
  //   const data = await res.json()
  //   if (!res.ok) { error.value = data.message ?? 'Authentication error'; return }
  //   localStorage.setItem('gainz_token', data.token)
  //   router.push('/tracker')

  // ── Demo placeholder: simulates a successful login for the presentation ──
  await new Promise(r => setTimeout(r, 600))
  if (email.value === 'demo@gainzscore.app' && password.value === 'password') {
    window.location.hash = '#/tracker'
  } else {
    error.value = 'Invalid credentials. Use demo@gainzscore.app / password'
  }
  loading.value = false
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-sm space-y-8">

      <!-- ── Logo ── -->
      <div class="text-center space-y-1">
        <p class="text-[10px] tracking-[0.4em] text-white/30 uppercase">Your League Tracker</p>
        <h1 class="text-4xl font-bold tracking-widest">
          <span class="text-neon-green">GAINZ</span><span class="text-white">SCORE</span>
        </h1>
        <div class="w-16 h-px bg-neon-green/40 mx-auto mt-2" />
      </div>

      <!-- ── Card ── -->
      <div class="bg-dark-card border border-white/10 rounded-2xl p-6 space-y-5
                  shadow-[0_0_40px_rgba(34,255,119,0.06)]">

        <!-- Error -->
        <div
          v-if="error"
          class="bg-red-500/10 border border-red-500/30 rounded-lg px-4 py-2
                 text-red-400 text-xs text-center tracking-wide"
        >
          {{ error }}
        </div>

        <!-- Email -->
        <div class="space-y-1.5">
          <label class="text-[10px] tracking-widest text-white/40 uppercase font-bold">
            Email
          </label>
          <input
            v-model="email"
            type="email"
            placeholder="demo@gainzscore.app"
            autocomplete="email"
            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3
                   text-white text-sm placeholder:text-white/20
                   focus:outline-none focus:border-neon-green focus:ring-1 focus:ring-neon-green/30
                   transition-colors"
            @keyup.enter="handleLogin"
          />
        </div>

        <!-- Password -->
        <div class="space-y-1.5">
          <label class="text-[10px] tracking-widest text-white/40 uppercase font-bold">
            Password
          </label>
          <input
            v-model="password"
            type="password"
            placeholder="••••••••"
            autocomplete="current-password"
            class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3
                   text-white text-sm placeholder:text-white/20
                   focus:outline-none focus:border-neon-green focus:ring-1 focus:ring-neon-green/30
                   transition-colors"
            @keyup.enter="handleLogin"
          />
        </div>

        <!-- Submit -->
        <button
          :disabled="loading"
          class="w-full py-3.5 rounded-xl bg-neon-green text-dark-bg font-bold text-sm
                 tracking-widest uppercase transition-all duration-200
                 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed
                 shadow-[0_0_24px_rgba(34,255,119,0.3)] hover:shadow-[0_0_32px_rgba(34,255,119,0.45)]"
          @click="handleLogin"
        >
          <span v-if="loading">
            <svg class="inline w-4 h-4 animate-spin mr-2" viewBox="0 0 24 24" fill="none">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            Signing in...
          </span>
          <span v-else>Sign In</span>
        </button>
      </div>

      <!-- ── Hint ── -->
      <p class="text-center text-[10px] text-white/20 tracking-wider">
        Demo: demo@gainzscore.app / password
      </p>

    </div>
  </div>
</template>
