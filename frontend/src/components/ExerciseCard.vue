<script setup>
import { ref } from 'vue'
import { getExerciseImage } from '../utils/imageUrl'

const props = defineProps({
  exerciseName:  { type: String, required: true },
  exerciseImage: { type: String, default: null },
})


const sets = ref([
  { id: 1, weight: '', reps: '', saved: false },
])

function addSet() {
  sets.value.push({ id: Date.now(), weight: '', reps: '', saved: false })
}

function completeSet(set) {
  if (set.weight && set.reps) {
    set.saved = true
  }
}
</script>

<template>
  <div class="card">
    <div class="flex items-center mb-4">
      <div class="w-10 h-10 rounded-full overflow-hidden bg-white/5 flex-shrink-0 mr-4 border border-white/10 flex items-center justify-center">
        <img
          :src="getExerciseImage({ name: exerciseName, image: exerciseImage })"
          :alt="exerciseName"
          class="w-full h-full object-cover"
          @error="(e) => e.target.style.display = 'none'"
        />
      </div>
      <h2 class="text-base font-bold tracking-wide uppercase text-white">
        {{ exerciseName }}
      </h2>
    </div>

    <div class="grid grid-cols-[2rem_1fr_1fr_2.5rem] gap-x-2 mb-1 px-1">
      <span class="text-[10px] font-bold tracking-widest text-white/40 uppercase text-center">SATZ</span>
      <span class="text-[10px] font-bold tracking-widest text-white/40 uppercase text-center">KG</span>
      <span class="text-[10px] font-bold tracking-widest text-white/40 uppercase text-center">WDH.</span>
      <span></span>
    </div>

    <div class="space-y-2">
      <div
        v-for="(set, i) in sets"
        :key="set.id"
        class="grid grid-cols-[2rem_1fr_1fr_2.5rem] gap-x-2 items-center"
      >
        <span
          class="text-xs font-bold text-center"
          :class="set.saved ? 'text-neon-green' : 'text-white/40'"
        >
          {{ i + 1 }}
        </span>

        <input
          v-model="set.weight"
          type="number"
          placeholder="0"
          :disabled="set.saved"
          class="w-full rounded-lg bg-white/5 border border-white/10 text-white text-sm text-center
                 py-2 px-1 focus:outline-none focus:border-neon-green transition-colors
                 placeholder:text-white/20 disabled:opacity-50"
        />

        <input
          v-model="set.reps"
          type="number"
          placeholder="0"
          :disabled="set.saved"
          class="w-full rounded-lg bg-white/5 border border-white/10 text-white text-sm text-center
                 py-2 px-1 focus:outline-none focus:border-neon-green transition-colors
                 placeholder:text-white/20 disabled:opacity-50"
        />

        <button
          class="w-9 h-9 rounded-full flex items-center justify-center border-2 transition-all
                 active:scale-90"
          :class="set.saved
            ? 'bg-neon-green border-neon-green text-dark-bg'
            : 'border-white/30 text-white/40 hover:border-neon-green hover:text-neon-green'"
          @click="completeSet(set)"
        >
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </button>
      </div>
    </div>

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
