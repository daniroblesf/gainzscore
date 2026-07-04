<script setup>
import { computed, onMounted, ref } from 'vue'
import {
  Activity,
  Dumbbell,
  ChevronRight,
  Footprints,
  HandFist,
  PersonStanding,
  Search,
  ScanHeart,
  Trash2,
  Weight,
  Zap,
  X,
} from '@lucide/vue'
import { API_BASE_URL } from '../utils/api'
import { getExerciseImage } from '../utils/imageUrl'

const props = defineProps({
  userId: { type: Number, required: true },
})

const emit = defineEmits(['select', 'close'])

const searchQuery = ref('')
const exercises = ref([])
const isLoading = ref(true)
const customName = ref('')
const customCategory = ref('Custom')
const customMultiplier = ref(1)
const customMessage = ref('')
const isCreatingCustom = ref(false)

const categoryOptions = ['Chest', 'Back', 'Arms', 'Legs', 'Shoulders', 'Core', 'Cardio', 'Custom']

onMounted(async () => {
  try {
    const response = await fetch(`${API_BASE_URL}/exercises?user_id=${props.userId}`)
    exercises.value = await response.json()
  } finally {
    isLoading.value = false
  }
})

const filteredExercises = computed(() => {
  const q = searchQuery.value.toLowerCase().trim()

  if (!q) return exercises.value

  return exercises.value.filter(ex => {
    return ex.name.toLowerCase().includes(q)
      || ex.category.toLowerCase().includes(q)
  })
})

const groupedExercises = computed(() => {
  const map = {}

  for (const ex of filteredExercises.value) {
    if (!map[ex.category]) map[ex.category] = []
    map[ex.category].push(ex)
  }

  return Object.entries(map)
    .sort(([a], [b]) => a.localeCompare(b))
    .map(([category, items]) => ({ category, exercises: items }))
})

function categoryMeta(category = '') {
  const normalized = category.toLowerCase()

  if (normalized.includes('chest') || normalized.includes('brust')) {
    return { icon: ScanHeart, label: 'Chest', hint: 'Push', color: 'text-red-300', bg: 'bg-red-400/10', border: 'border-red-400/30' }
  }

  if (normalized.includes('back') || normalized.includes('ruecken') || normalized.includes('rucken')) {
    return { icon: PersonStanding, label: 'Back', hint: 'Pull', color: 'text-cyan-300', bg: 'bg-cyan-400/10', border: 'border-cyan-400/30' }
  }

  if (normalized.includes('arm') || normalized.includes('bizeps') || normalized.includes('trizeps')) {
    return { icon: HandFist, label: 'Arms', hint: 'Strength', color: 'text-purple-300', bg: 'bg-purple-400/10', border: 'border-purple-400/30' }
  }

  if (normalized.includes('leg') || normalized.includes('bein')) {
    return { icon: Footprints, label: 'Legs', hint: 'Power', color: 'text-yellow-300', bg: 'bg-yellow-400/10', border: 'border-yellow-400/30' }
  }

  if (normalized.includes('shoulder') || normalized.includes('schulter')) {
    return { icon: Weight, label: 'Shoulders', hint: 'Press', color: 'text-orange-300', bg: 'bg-orange-400/10', border: 'border-orange-400/30' }
  }

  if (normalized.includes('core') || normalized.includes('abs') || normalized.includes('bauch')) {
    return { icon: Activity, label: 'Core', hint: 'Control', color: 'text-lime-300', bg: 'bg-lime-400/10', border: 'border-lime-400/30' }
  }

  if (normalized.includes('cardio')) {
    return { icon: Zap, label: 'Cardio', hint: 'Condition', color: 'text-pink-300', bg: 'bg-pink-400/10', border: 'border-pink-400/30' }
  }

  if (normalized.includes('custom') || normalized.includes('eigene')) {
    return { icon: Activity, label: 'Custom', hint: 'Individuell', color: 'text-slate-200', bg: 'bg-slate-400/10', border: 'border-slate-400/25' }
  }

  return { icon: Dumbbell, label: category, hint: 'Exercise', color: 'text-neon-green', bg: 'bg-neon-green/10', border: 'border-neon-green/30' }
}

function isOwnExercise(exercise) {
  return Number(exercise.user_id) === Number(props.userId)
}

async function createCustomExercise() {
  customMessage.value = ''

  if (customName.value.trim().length < 2) {
    customMessage.value = 'Bitte Namen eingeben.'
    return
  }

  isCreatingCustom.value = true

  try {
    const response = await fetch(`${API_BASE_URL}/exercises`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify({
        user_id: props.userId,
        name: customName.value.trim(),
        category: customCategory.value,
        xp_multiplier: Number(customMultiplier.value) || 1,
      }),
    })

    if (response.status === 422) {
      customMessage.value = 'Diese Uebung gibt es schon oder die Eingaben sind ungueltig.'
      return
    }

    if (!response.ok) {
      customMessage.value = 'Uebung konnte nicht erstellt werden.'
      return
    }

    const exercise = await response.json()
    exercises.value.push(exercise)
    emit('select', exercise)
  } catch {
    customMessage.value = 'Backend ist nicht erreichbar.'
  } finally {
    isCreatingCustom.value = false
  }
}

async function deleteCustomExercise(exercise) {
  if (!isOwnExercise(exercise)) return

  try {
    const response = await fetch(`${API_BASE_URL}/exercises/${exercise.id}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      body: JSON.stringify({
        user_id: props.userId,
      }),
    })

    if (!response.ok) {
      customMessage.value = 'Uebung konnte nicht geloescht werden.'
      return
    }

    exercises.value = exercises.value.filter(item => item.id !== exercise.id)
    customMessage.value = ''
  } catch {
    customMessage.value = 'Backend ist nicht erreichbar.'
  }
}
</script>

<template>
  <div class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex justify-center">
    <div class="w-full max-w-md bg-dark-bg h-full flex flex-col font-mono text-white shadow-2xl">
      <div class="flex-none px-4 pt-5 pb-4 border-b border-white/10 space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-[10px] tracking-widest uppercase text-white/30">Workout Builder</p>
            <h2 class="text-lg font-bold leading-none text-white">
              Uebung waehlen
            </h2>
          </div>

          <button
            class="w-9 h-9 flex items-center justify-center rounded-xl
                   bg-white/5 border border-white/10 text-white/50
                   hover:text-white hover:border-white/30 active:scale-90 transition-all"
            @click="emit('close')"
          >
            <X class="w-4 h-4" />
          </button>
        </div>

        <div class="relative">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/25" stroke-width="2.4" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Uebung oder Muskelgruppe suchen"
            class="input-field py-3 pl-10"
          />
        </div>

        <div class="grid grid-cols-2 gap-2">
          <div class="rounded-xl border border-white/10 bg-white/5 px-3 py-2">
            <p class="text-[9px] tracking-widest uppercase text-white/30">Uebungen</p>
            <p class="text-lg font-extrabold text-white tabular-nums">{{ filteredExercises.length }}</p>
          </div>
          <div class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-right">
            <p class="text-[9px] tracking-widest uppercase text-white/30">Gruppen</p>
            <p class="text-lg font-extrabold text-neon-green tabular-nums">{{ groupedExercises.length }}</p>
          </div>
        </div>
      </div>

      <div class="flex-1 overflow-y-auto px-4 pb-6">
        <div v-if="isLoading" class="py-20 text-center">
          <div class="mx-auto mb-4 w-12 h-12 rounded-2xl border border-neon-green/30 bg-neon-green/10 flex items-center justify-center">
            <Dumbbell class="w-5 h-5 text-neon-green animate-pulse" stroke-width="2.4" />
          </div>
          <p class="text-white/25 text-xs tracking-widest uppercase">Laedt Uebungen</p>
        </div>

        <template v-else>
          <section
            v-for="cat in groupedExercises"
            :key="cat.category"
            class="pt-5 space-y-2"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span
                  class="w-8 h-8 rounded-xl border flex items-center justify-center"
                  :class="[categoryMeta(cat.category).border, categoryMeta(cat.category).bg]"
                >
                  <component
                    :is="categoryMeta(cat.category).icon"
                    class="w-4 h-4"
                    :class="categoryMeta(cat.category).color"
                    stroke-width="2.4"
                  />
                </span>
                <div>
                  <p class="text-xs font-bold text-white uppercase tracking-wide">
                    {{ categoryMeta(cat.category).label }}
                  </p>
                  <p class="text-[9px] tracking-widest uppercase text-white/25">
                    {{ categoryMeta(cat.category).hint }} · {{ cat.exercises.length }} Optionen
                  </p>
                </div>
              </div>
              <div class="h-px flex-1 ml-3 bg-white/10"></div>
            </div>

            <div
              v-for="ex in cat.exercises"
              :key="ex.id"
              role="button"
              tabindex="0"
              class="w-full group grid grid-cols-[3.2rem_1fr_auto] gap-3 items-center text-left
                     rounded-2xl border border-white/10 bg-black/35 px-3 py-3
                     hover:border-neon-green/40 hover:bg-white/5
                     active:scale-[0.99] transition-all"
              @click="emit('select', ex)"
              @keydown.enter.prevent="emit('select', ex)"
              @keydown.space.prevent="emit('select', ex)"
            >
              <div class="relative w-12 h-12 rounded-xl overflow-hidden bg-white/5 flex-shrink-0 border border-white/10 flex items-center justify-center">
                <component
                  :is="categoryMeta(ex.category).icon"
                  class="w-5 h-5"
                  :class="categoryMeta(ex.category).color"
                  stroke-width="2.4"
                />
                <img
                  :src="getExerciseImage(ex)"
                  :alt="ex.name"
                  class="absolute inset-0 w-full h-full object-cover"
                  @error="(e) => e.target.style.display = 'none'"
                />
              </div>

              <div class="min-w-0">
                <p class="text-sm font-bold text-white truncate">
                  {{ ex.name }}
                </p>
                <div class="mt-1 flex items-center gap-2">
                  <span
                    class="text-[9px] font-bold tracking-widest uppercase px-2 py-0.5 rounded border"
                    :class="[categoryMeta(ex.category).color, categoryMeta(ex.category).border, categoryMeta(ex.category).bg]"
                  >
                    {{ categoryMeta(ex.category).label }}
                  </span>
                  <span class="text-[9px] tracking-widest uppercase text-white/25">
                    {{ categoryMeta(ex.category).hint }} · {{ Number(ex.xp_multiplier || 1).toFixed(1) }}x XP
                  </span>
                </div>
              </div>

              <button
                v-if="isOwnExercise(ex)"
                type="button"
                class="w-9 h-9 rounded-xl border border-white/10 bg-white/5 text-white/30
                       flex items-center justify-center hover:border-red-400/40 hover:text-red-300
                       transition-colors active:scale-95"
                title="Eigene Uebung loeschen"
                @click.stop="deleteCustomExercise(ex)"
              >
                <Trash2 class="w-4 h-4" stroke-width="2.4" />
              </button>
              <ChevronRight
                v-else
                class="w-4 h-4 text-white/20 group-hover:text-neon-green transition-colors"
                stroke-width="2.4"
              />
            </div>
          </section>

          <div v-if="groupedExercises.length === 0" class="py-20 text-center">
            <div class="mx-auto mb-4 w-12 h-12 rounded-2xl border border-white/10 bg-white/5 flex items-center justify-center">
              <Search class="w-5 h-5 text-white/20" stroke-width="2.4" />
            </div>
            <p class="text-white/25 text-xs tracking-widest uppercase">Keine Uebungen gefunden</p>
          </div>

          <section class="pt-5 space-y-3">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl border border-slate-400/25 bg-slate-400/10 flex items-center justify-center">
                  <Activity class="w-4 h-4 text-slate-200" stroke-width="2.4" />
                </span>
                <div>
                  <p class="text-xs font-bold text-white uppercase tracking-wide">Eigene Uebung</p>
                  <p class="text-[9px] tracking-widest uppercase text-white/25">Nicht in der Liste?</p>
                </div>
              </div>
              <div class="h-px flex-1 ml-3 bg-white/10"></div>
            </div>

            <form class="rounded-2xl border border-white/10 bg-black/35 p-3 space-y-3" @submit.prevent="createCustomExercise">
              <input
                v-model="customName"
                type="text"
                placeholder="Name der Uebung"
                class="input-field py-2.5"
              />

              <div class="grid grid-cols-[1fr_6rem] gap-2">
                <select
                  v-model="customCategory"
                  class="input-field py-2.5"
                >
                  <option
                    v-for="category in categoryOptions"
                    :key="category"
                    :value="category"
                    class="bg-dark-bg"
                  >
                    {{ category }}
                  </option>
                </select>

                <input
                  v-model="customMultiplier"
                  type="number"
                  min="0.5"
                  max="2"
                  step="0.1"
                  class="input-field py-2.5 text-center"
                />
              </div>

              <button
                type="submit"
                class="primary-action py-3"
                :disabled="isCreatingCustom"
              >
                {{ isCreatingCustom ? 'Erstellt' : 'Eigene Uebung nutzen' }}
              </button>

              <p
                v-if="customMessage"
                class="text-center text-[10px] tracking-widest uppercase text-red-300"
              >
                {{ customMessage }}
              </p>
            </form>
          </section>
        </template>
      </div>
    </div>
  </div>
</template>
