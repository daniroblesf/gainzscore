<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentXp: { type: Number, default: 0 },
  totalXp:   { type: Number, default: 1000 },
})

const percent = computed(() =>
  Math.min((props.currentXp / props.totalXp) * 100, 100)
)
</script>

<template>
  <div class="w-full">
    <div class="flex justify-between mb-1.5">
      <span class="text-xs text-white/50 tabular-nums">
        {{ currentXp.toLocaleString() }} XP
      </span>
      <span class="text-xs text-white/30 tabular-nums">
        / {{ totalXp.toLocaleString() }}
      </span>
    </div>

    <div class="relative h-2.5 w-full rounded-full bg-white/10 overflow-hidden">
      <div
        class="h-full rounded-full bg-neon-green transition-all duration-700 ease-out"
        :style="{ width: percent + '%' }"
      />
    </div>
  </div>
</template>
