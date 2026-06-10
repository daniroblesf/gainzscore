<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router   = useRouter()
const email    = ref('')
const password = ref('')

function handleLogin() {
  router.push('/home')
}
</script>

<template>
  <div
    class="min-h-screen w-full bg-cover bg-center bg-no-repeat flex items-center justify-center"
    style="background-image: linear-gradient(rgba(0,0,0,0.82), rgba(0,0,0,0.95)),
           url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1200&auto=format&fit=crop');"
  >
    <div class="w-full max-w-md mx-auto px-4 py-10 space-y-8 text-white">

      <!-- ── Logo ── -->
      <div class="text-center space-y-1">
        <h1 class="text-3xl font-bold tracking-tight leading-none">
          <span class="text-neon-green">GAINZ</span><span class="text-white">SCORE</span>
        </h1>
        <p class="text-[10px] tracking-widest text-white/30 uppercase">Level up your gym life</p>
      </div>

      <!-- ── Login card ── -->
      <div class="bg-black/60 backdrop-blur-md border border-white/10 rounded-2xl p-6 space-y-5 shadow-lg">

        <div class="space-y-1">
          <p class="text-[10px] tracking-widest text-white/30 uppercase">Welcome back</p>
          <h2 class="text-lg font-bold leading-none text-white">Sign in to continue</h2>
        </div>

        <form class="space-y-4" @submit.prevent="handleLogin">

          <!-- Email -->
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
              class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3
                     text-sm text-white placeholder-white/20 font-mono
                     focus:outline-none focus:border-neon-green focus:ring-1 focus:ring-neon-green
                     transition-colors"
            />
          </div>

          <!-- Password -->
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
              placeholder="••••••••"
              class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3
                     text-sm text-white placeholder-white/20 font-mono
                     focus:outline-none focus:border-neon-green focus:ring-1 focus:ring-neon-green
                     transition-colors"
            />
          </div>

          <button
            type="submit"
            class="w-full py-4 rounded-2xl bg-neon-green text-dark-bg font-bold text-sm
                   tracking-widest uppercase active:scale-95 transition-all
                   shadow-[0_0_24px_rgba(34,255,119,0.3)] hover:shadow-[0_0_36px_rgba(34,255,119,0.45)]"
          >
            Login
          </button>

        </form>
      </div>


    </div>
  </div>
</template>
