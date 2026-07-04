<script setup>
import { computed, onMounted, ref } from 'vue'
import { X } from '@lucide/vue'
import { API_BASE_URL } from '../utils/api'
import { getExerciseImage } from '../utils/imageUrl'

const emit = defineEmits(['select', 'close'])

const searchQuery = ref('')
const exercises = ref([])
const isLoading = ref(true)

onMounted(async () => {
  try {
    const response = await fetch(`${API_BASE_URL}/exercises`)
    exercises.value = await response.json()
  } finally {
    isLoading.value = false
  }
})

const groupedExercises = computed(() => {
  const q = searchQuery.value.toLowerCase().trim()
  const filtered = q
    ? exercises.value.filter(ex => ex.name.toLowerCase().includes(q))
    : exercises.value

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
  <div class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex justify-center">
    <div class="w-full max-w-md bg-dark-bg h-full flex flex-col font-mono text-white shadow-2xl">
      <div class="flex-none px-4 pt-5 pb-4 border-b border-white/10 space-y-3">
        <div class="flex items-center justify-between">
          <h2 class="text-xs font-bold tracking-widest uppercase text-white">
            Uebung waehlen
          </h2>

          <button
            class="w-8 h-8 flex items-center justify-center rounded-full
                   bg-white/5 border border-white/10 text-white/50
                   hover:text-white hover:border-white/30 active:scale-90 transition-all"
            @click="emit('close')"
          >
            <X class="w-4 h-4" />
          </button>
        </div>

        <input
          v-model="searchQuery"
          type="text"
          placeholder="Uebung suchen"
          class="input-field py-2.5"
        />
      </div>

      <div class="flex-1 overflow-y-auto">
        <div v-if="isLoading" class="py-20 text-center">
          <p class="text-white/20 text-xs tracking-widest uppercase">Laedt Uebungen</p>
        </div>

        <template v-else>
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
            <p class="text-white/20 text-xs tracking-widest uppercase">Keine Uebungen gefunden</p>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>
