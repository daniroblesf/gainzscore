<script setup>
const props = defineProps({
  rank:       { type: String,  default: 'SILBER I' },
  currentXp:  { type: Number,  default: 65 },
  totalXp:    { type: Number,  default: 1000 },
})

const percent = computed(() => {
  const val = Math.min((props.currentXp / props.totalXp) * 100, 100)
  return Math.max(val, 0)
})

import { computed } from 'vue'
</script>

<template>
  <div class="w-full">
    <!-- Rank label + XP text -->
    <div class="flex items-center justify-between mb-1.5">
      <span class="text-xs font-bold tracking-widest text-neon-green uppercase">
        {{ rank }}
      </span>
      <span class="text-xs text-white/50 tabular-nums">
        {{ currentXp.toLocaleString() }} / {{ totalXp.toLocaleString() }} XP
      </span>
    </div>

    <!-- Track -->
    <div class="relative h-2.5 w-full rounded-full bg-white/10 overflow-hidden">
      <!-- Fill -->
      <div
        class="h-full rounded-full bg-neon-green transition-all duration-700 ease-out"
        :style="{ width: percent + '%' }"
      />
      <!-- Glow overlay -->
      <div
        class="absolute inset-0 rounded-full"
        :style="{
          background: `linear-gradient(90deg, transparent ${percent - 2}%, rgba(34,255,119,0.45) ${percent}%, transparent ${percent + 2}%)`
        }"
      />
    </div>
  </div>
</template>
