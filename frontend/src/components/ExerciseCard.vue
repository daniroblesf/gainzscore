<script setup>
import { computed } from 'vue'
import { Check, Minus, Plus, RotateCcw } from '@lucide/vue'
import { getExerciseImage } from '../utils/imageUrl'

const props = defineProps({
  exerciseName: { type: String, required: true },
  exerciseImage: { type: String, default: null },
  sets: {
    type: Array,
    required: true,
  },
})

const emit = defineEmits(['updateSets'])

const MAX_SETS = 12
const KG_MIN = 1
const KG_MAX = 500
const KG_STEP = 2.5
const REPS_MIN = 1
const REPS_MAX = 100
const REPS_STEP = 1

const completedSets = computed(() => props.sets.filter(set => set.completed).length)
const totalVolume = computed(() => props.sets.reduce((sum, set) => {
  const kg = Number(set.kg)
  const reps = Number(set.reps)
  if (!Number.isFinite(kg) || !Number.isFinite(reps)) return sum
  return sum + kg * reps
}, 0))

function parseNumberInput(value) {
  if (value === '') return ''
  return Number(value)
}

function clamp(value, min, max) {
  return Math.min(Math.max(value, min), max)
}

function isValidSet(set) {
  const kg = Number(set.kg)
  const reps = Number(set.reps)

  return Number.isFinite(kg)
    && Number.isFinite(reps)
    && kg >= KG_MIN
    && kg <= KG_MAX
    && reps >= REPS_MIN
    && reps <= REPS_MAX
}

function replaceSets(updater) {
  emit('updateSets', props.sets.map(updater))
}

function updateSet(index, field, value) {
  replaceSets((set, i) => {
    if (i !== index) return set

    return {
      ...set,
      [field]: value,
      completed: false,
    }
  })
}

function stepSet(index, field, direction) {
  const min = field === 'kg' ? KG_MIN : REPS_MIN
  const max = field === 'kg' ? KG_MAX : REPS_MAX
  const step = field === 'kg' ? KG_STEP : REPS_STEP
  const current = Number(props.sets[index][field]) || min
  const nextValue = clamp(current + step * direction, min, max)

  updateSet(index, field, nextValue)
}

function addSet() {
  if (props.sets.length >= MAX_SETS) {
    alert(`Maximal ${MAX_SETS} Saetze pro Uebung.`)
    return
  }

  const previousSet = props.sets[props.sets.length - 1]
  const updatedSets = [
    ...props.sets,
    {
      set_number: props.sets.length + 1,
      kg: previousSet?.kg || '',
      reps: previousSet?.reps || '',
      completed: false,
    },
  ]

  emit('updateSets', updatedSets)
}

function completeSet(index) {
  const set = props.sets[index]

  if (!isValidSet(set)) {
    alert(`Bitte echte Werte eingeben: KG ${KG_MIN}-${KG_MAX}, Wiederholungen ${REPS_MIN}-${REPS_MAX}.`)
    return
  }

  replaceSets((currentSet, i) => {
    if (i !== index) return currentSet

    return {
      ...currentSet,
      completed: true,
    }
  })
}

function editSet(index) {
  replaceSets((set, i) => {
    if (i !== index) return set

    return {
      ...set,
      completed: false,
    }
  })
}
</script>

<template>
  <div class="card space-y-4">
    <div class="flex items-start gap-3">
      <div
        class="w-12 h-12 rounded-xl overflow-hidden bg-white/5 flex-shrink-0
               border border-white/10 flex items-center justify-center"
      >
        <img
          :src="getExerciseImage({ name: exerciseName, image: exerciseImage })"
          :alt="exerciseName"
          class="w-full h-full object-cover"
          @error="(e) => e.target.style.display = 'none'"
        />
      </div>

      <div class="min-w-0 flex-1">
        <p class="text-[10px] tracking-widest uppercase text-white/30">Uebung</p>
        <h2 class="text-base font-bold tracking-wide uppercase text-white truncate">
          {{ exerciseName }}
        </h2>

        <div class="mt-2 grid grid-cols-2 gap-2">
          <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2">
            <p class="text-[9px] tracking-widest uppercase text-white/30">Saetze</p>
            <p class="text-sm font-bold text-white tabular-nums">{{ completedSets }}/{{ sets.length }}</p>
          </div>
          <div class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-right">
            <p class="text-[9px] tracking-widest uppercase text-white/30">Volumen</p>
            <p class="text-sm font-bold text-neon-green tabular-nums">{{ totalVolume.toLocaleString() }} kg</p>
          </div>
        </div>
      </div>
    </div>

    <div class="space-y-2">
      <div
        v-for="(set, i) in sets"
        :key="i"
        class="rounded-xl border p-3 transition-colors"
        :class="set.completed
          ? 'border-neon-green/35 bg-neon-green/10'
          : 'border-white/10 bg-white/5'"
      >
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center gap-2">
            <span
              class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold border"
              :class="set.completed
                ? 'text-dark-bg bg-neon-green border-neon-green'
                : 'text-white/40 bg-black/20 border-white/10'"
            >
              {{ i + 1 }}
            </span>
            <div>
              <p class="text-xs font-bold text-white">Satz {{ i + 1 }}</p>
              <p
                class="text-[9px] tracking-widest uppercase"
                :class="set.completed ? 'text-neon-green/70' : 'text-white/25'"
              >
                {{ set.completed ? 'Abgeschlossen' : 'Offen' }}
              </p>
            </div>
          </div>

          <button
            v-if="set.completed"
            class="w-8 h-8 rounded-lg border border-white/10 text-white/45
                   flex items-center justify-center hover:text-neon-green hover:border-neon-green/40
                   transition-colors active:scale-95"
            title="Satz bearbeiten"
            @click="editSet(i)"
          >
            <RotateCcw class="w-4 h-4" stroke-width="2.4" />
          </button>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div class="space-y-1.5">
            <label class="block text-[9px] tracking-widest uppercase text-white/35">KG</label>
            <div class="grid grid-cols-[2rem_1fr_2rem] gap-1">
              <button
                class="rounded-lg border border-white/10 bg-black/25 text-white/45 flex items-center justify-center
                       hover:text-neon-green hover:border-neon-green/40 disabled:opacity-30 transition-colors"
                :disabled="set.completed"
                @click="stepSet(i, 'kg', -1)"
              >
                <Minus class="w-3.5 h-3.5" />
              </button>
              <input
                :value="set.kg"
                type="number"
                placeholder="0"
                :min="KG_MIN"
                :max="KG_MAX"
                :step="KG_STEP"
                :disabled="set.completed"
                class="input-field py-2 px-1 text-center rounded-lg"
                @input="updateSet(i, 'kg', parseNumberInput($event.target.value))"
              />
              <button
                class="rounded-lg border border-white/10 bg-black/25 text-white/45 flex items-center justify-center
                       hover:text-neon-green hover:border-neon-green/40 disabled:opacity-30 transition-colors"
                :disabled="set.completed"
                @click="stepSet(i, 'kg', 1)"
              >
                <Plus class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>

          <div class="space-y-1.5">
            <label class="block text-[9px] tracking-widest uppercase text-white/35">Wdh.</label>
            <div class="grid grid-cols-[2rem_1fr_2rem] gap-1">
              <button
                class="rounded-lg border border-white/10 bg-black/25 text-white/45 flex items-center justify-center
                       hover:text-neon-green hover:border-neon-green/40 disabled:opacity-30 transition-colors"
                :disabled="set.completed"
                @click="stepSet(i, 'reps', -1)"
              >
                <Minus class="w-3.5 h-3.5" />
              </button>
              <input
                :value="set.reps"
                type="number"
                placeholder="0"
                :min="REPS_MIN"
                :max="REPS_MAX"
                :step="REPS_STEP"
                :disabled="set.completed"
                class="input-field py-2 px-1 text-center rounded-lg"
                @input="updateSet(i, 'reps', parseNumberInput($event.target.value))"
              />
              <button
                class="rounded-lg border border-white/10 bg-black/25 text-white/45 flex items-center justify-center
                       hover:text-neon-green hover:border-neon-green/40 disabled:opacity-30 transition-colors"
                :disabled="set.completed"
                @click="stepSet(i, 'reps', 1)"
              >
                <Plus class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>
        </div>

        <div class="mt-3 flex items-center justify-between gap-3">
          <p class="text-[10px] tracking-widest uppercase text-white/30 tabular-nums">
            {{ Number(set.kg) || 0 }} x {{ Number(set.reps) || 0 }} = {{ ((Number(set.kg) || 0) * (Number(set.reps) || 0)).toLocaleString() }} kg
          </p>

          <button
            class="h-9 px-3 rounded-lg flex items-center justify-center gap-1.5 border text-xs font-bold uppercase tracking-widest transition-all active:scale-95"
            :class="set.completed
              ? 'border-neon-green bg-neon-green text-dark-bg'
              : 'border-white/20 text-white/45 hover:border-neon-green hover:text-neon-green'"
            @click="set.completed ? editSet(i) : completeSet(i)"
          >
            <Check class="w-4 h-4" stroke-width="3" />
            <span>{{ set.completed ? 'Fertig' : 'OK' }}</span>
          </button>
        </div>
      </div>
    </div>

    <button
      class="w-full py-3 rounded-xl border border-dashed border-white/20
             text-xs font-bold tracking-widest text-white/40 uppercase
             hover:border-neon-green hover:text-neon-green transition-colors disabled:opacity-40"
      :disabled="sets.length >= MAX_SETS"
      @click="addSet"
    >
      + Satz hinzufuegen
    </button>
  </div>
</template>
