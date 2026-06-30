<script setup>
import { ref, computed, onMounted } from 'vue'
import { X } from '@lucide/vue'
import { getExerciseImage } from '../utils/imageUrl'

const emit = defineEmits(['select', 'close'])

const searchQuery = ref('')

// Flat list populated from the API on mount.
const exercises = ref([])

// On mount, fetch all exercises from the Laravel API and store them.
onMounted(async () => {
  const response = await fetch('http://localhost:8000/api/exercises')
  exercises.value = await response.json()
})


// Group the flat exercises array by category, applying the search filter first.
const groupedExercises = computed(() => {
  const q = searchQuery.value.toLowerCase().trim()

  const filtered = q
    ? exercises.value.filter(ex => ex.name.toLowerCase().includes(q))
    : exercises.value

  // Build a { category -> exercises[] } map, then convert to a sorted array.
  const map = {}
  for (const ex of filtered) {
    if (!map[ex.category]) map[ex.category] = []
    map[ex.category].push(ex)
  }

  return Object.entries(map)
    .sort(([a], [b]) => a.localeCompare(b))
    .map(([category, items]) => ({ category, exercises: items }))
})
</script>

<template>

  <div class="fixed inset-0 z-50 bg-black/80 flex justify-center">
    <div class="w-full max-w-md bg-dark-bg h-full flex flex-col font-mono text-white">

      <div class="flex-none px-4 pt-5 pb-4 border-b border-white/5 space-y-3">

        <!-- Título + botón cerrar -->
        <div class="flex items-center justify-between">
          <h2 class="text-xs font-bold tracking-widest uppercase text-white">
            Übung wählen
          </h2>
          <button
            class="w-8 h-8 flex items-center justify-center rounded-full
                   bg-white/5 border border-white/10 text-white/50
                   hover:text-white hover:border-white/30 active:scale-90 transition-all"
            @click="$emit('close')"
          >
            <X class="w-4 h-4" />
          </button>
        </div>

        <input
          v-model="searchQuery"
          type="text"
          placeholder="Übung Suchen"
          class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5
                 text-sm text-white placeholder:text-white/25
                 focus:outline-none focus:ring-1 focus:ring-neon-green focus:border-neon-green/50
                 transition-colors"
        />
      </div>

      <div class="flex-1 overflow-y-auto">

        <div v-for="cat in groupedExercises" :key="cat.category">

          <div class="flex items-center gap-2 px-4 pt-5 pb-2">
            <span class="text-[10px] font-bold tracking-widest uppercase text-neon-green/70">
              {{ cat.category }}
            </span>
            <div class="flex-1 h-px bg-white/5"></div>
          </div>

          <button
            v-for="ex in cat.exercises"
            :key="ex.id"
            class="w-full flex items-center gap-4 px-4 py-3 text-left
                   border-b border-white/10 hover:bg-white/5
                   active:bg-white/10 transition-colors"
            @click="emit('select', ex)"
          >
            <div class="w-10 h-10 rounded-full overflow-hidden bg-white/5 flex-shrink-0 border border-white/10 flex items-center justify-center">
              <img
                :src="getExerciseImage(ex)"
                :alt="ex.name"
                class="w-full h-full object-cover"
                @error="(e) => e.target.style.display = 'none'"
              />
            </div>

            <span class="flex-1 text-sm font-semibold text-white truncate">
              {{ ex.name }}
            </span>
          </button>
        </div>

        <div v-if="groupedExercises.length === 0" class="py-20 text-center">
          <p class="text-white/20 text-xs tracking-widest uppercase">Keine Übungen gefunden</p>
        </div>
      </div>

    </div>
  </div>
</template>
