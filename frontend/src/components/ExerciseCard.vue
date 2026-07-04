<script setup>
import { Check } from '@lucide/vue'
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

function updateSet(index, field, value) {
  const updatedSets = props.sets.map((set, i) => {
    if (i !== index) return set

    return {
      ...set,
      [field]: value,
    }
  })

  emit('updateSets', updatedSets)
}

function addSet() {
  const updatedSets = [
    ...props.sets,
    {
      set_number: props.sets.length + 1,
      kg: '',
      reps: '',
      completed: false,
    },
  ]

  emit('updateSets', updatedSets)
}

function completeSet(index) {
  const set = props.sets[index]

  if (!set.kg || !set.reps) {
    alert('Bitte KG und Wiederholungen eingeben.')
    return
  }

  const updatedSets = props.sets.map((currentSet, i) => {
    if (i !== index) return currentSet

    return {
      ...currentSet,
      completed: true,
    }
  })

  emit('updateSets', updatedSets)
}
</script>

<template>
  <div class="card">
    <div class="flex items-center mb-4">
      <div
        class="w-10 h-10 rounded-full overflow-hidden bg-white/5 flex-shrink-0 mr-4
               border border-white/10 flex items-center justify-center"
      >
        <img
          :src="getExerciseImage({ name: exerciseName, image: exerciseImage })"
          :alt="exerciseName"
          class="w-full h-full object-cover"
          @error="(e) => e.target.style.display = 'none'"
        />
      </div>

      <h2 class="text-base font-bold tracking-wide uppercase text-white truncate">
        {{ exerciseName }}
      </h2>
    </div>

    <div class="grid grid-cols-[2rem_1fr_1fr_2.5rem] gap-x-2 mb-1 px-1">
      <span class="text-[10px] font-bold tracking-widest text-white/40 uppercase text-center">Satz</span>
      <span class="text-[10px] font-bold tracking-widest text-white/40 uppercase text-center">KG</span>
      <span class="text-[10px] font-bold tracking-widest text-white/40 uppercase text-center">Wdh.</span>
      <span></span>
    </div>

    <div class="space-y-2">
      <div
        v-for="(set, i) in sets"
        :key="i"
        class="grid grid-cols-[2rem_1fr_1fr_2.5rem] gap-x-2 items-center"
      >
        <span
          class="text-xs font-bold text-center"
          :class="set.completed ? 'text-neon-green' : 'text-white/40'"
        >
          {{ i + 1 }}
        </span>

        <input
          :value="set.kg"
          type="number"
          placeholder="0"
          :disabled="set.completed"
          class="input-field py-2 px-1 text-center rounded-lg"
          @input="updateSet(i, 'kg', Number($event.target.value))"
        />

        <input
          :value="set.reps"
          type="number"
          placeholder="0"
          :disabled="set.completed"
          class="input-field py-2 px-1 text-center rounded-lg"
          @input="updateSet(i, 'reps', Number($event.target.value))"
        />

        <button
          class="w-9 h-9 rounded-full flex items-center justify-center border-2 transition-all active:scale-90"
          :class="set.completed
            ? 'bg-neon-green border-neon-green text-dark-bg'
            : 'border-white/30 text-white/40 hover:border-neon-green hover:text-neon-green'"
          @click="completeSet(i)"
        >
          <Check class="w-4 h-4" stroke-width="3" />
        </button>
      </div>
    </div>

    <button
      class="mt-4 w-full py-2 rounded-xl border border-dashed border-white/20
             text-xs font-bold tracking-widest text-white/40 uppercase
             hover:border-neon-green hover:text-neon-green transition-colors"
      @click="addSet"
    >
      + Satz hinzufuegen
    </button>
  </div>
</template>
