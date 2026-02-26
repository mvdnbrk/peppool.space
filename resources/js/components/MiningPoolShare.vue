<template>
  <div class="w-full h-full flex flex-col items-center justify-center">
    <div v-if="loading" class="animate-pulse text-gray-400">Loading pool data...</div>
    <div v-else-if="stats.length === 0" class="text-gray-400 text-sm italic">No data available for the selected period.</div>
    <div v-else class="flex flex-col md:flex-row items-center justify-around w-full">
      <!-- Simple SVG Pie Chart -->
      <div class="relative w-48 h-48 mb-6 md:mb-0">
        <svg viewBox="0 0 32 32" class="w-full h-full transform -rotate-90">
          <circle
            v-for="(slice, index) in slices"
            :key="index"
            r="16"
            cx="16"
            cy="16"
            fill="transparent"
            :stroke="slice.color"
            stroke-width="32"
            :stroke-dasharray="slice.strokeDasharray"
            class="transition-all duration-500"
          />
        </svg>
      </div>

      <!-- Legend -->
      <div class="flex flex-col space-y-2 max-w-xs w-full px-4">
        <div v-for="stat in stats" :key="stat.poolId" class="flex items-center justify-between text-sm">
          <div class="flex items-center space-x-2 truncate mr-4">
            <div class="w-3 h-3 rounded-full flex-shrink-0" :style="{ backgroundColor: getColor(stat.name) }"></div>
            <span class="font-medium text-gray-700 dark:text-gray-300 truncate">{{ stat.name }}</span>
          </div>
          <span class="text-gray-500 dark:text-gray-400 font-mono">{{ (stat.share * 100).toFixed(1) }}%</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

const props = defineProps({
  apiUrl: { type: String, required: true }
})

const stats = ref([])
const loading = ref(true)

const poolColors = {
  'ViaBTC': '#2ab1e2',
  'F2Pool': '#345ced',
  'AntPool': '#edae34',
  'KuPool': '#24ae8f',
  '00Hash': '#6366f1',
  'Unknown': '#9ca3af'
}

const getColor = (name) => poolColors[name] || '#cbd5e1'

const slices = computed(() => {
  let accumulatedPercent = 0
  return stats.value.map(stat => {
    const percent = stat.share * 100
    const startOffset = accumulatedPercent
    accumulatedPercent += percent
    
    // SVG stroke-dasharray works based on circumference (2 * PI * r)
    // For r=16, circumference ≈ 100.5. Let's just use 100 for simplicity in the viewbox.
    return {
      color: getColor(stat.name),
      strokeDasharray: `${percent} 100`,
      strokeDashoffset: -startOffset
    }
  })
})

const fetchStats = async () => {
  try {
    const response = await fetch(props.apiUrl)
    stats.value = await response.json()
  } catch (err) {
    console.error('Failed to fetch mining pool stats:', err)
  } finally {
    loading.ref = false
    loading.value = false
  }
}

onMounted(fetchStats)
</script>

<style scoped>
circle {
  stroke-dasharray: 0 100;
  stroke-dashoffset: 0;
}
</style>
