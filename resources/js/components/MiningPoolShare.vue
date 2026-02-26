<template>
  <div class="w-full h-full flex flex-col items-center justify-center">
    <div v-if="loading" class="animate-pulse text-gray-400">Loading pool data...</div>
    <div v-else-if="stats.length === 0" class="text-gray-400 text-sm italic">No data available for the selected period.</div>
    <div v-else class="flex flex-col md:flex-row items-center justify-around w-full max-w-2xl">
      <!-- Donut Chart -->
      <div class="relative w-64 h-64 mb-8 md:mb-0 group">
        <svg viewBox="0 0 100 100" class="w-full h-full transform -rotate-90 drop-shadow-sm">
          <!-- Background track -->
          <circle
            cx="50"
            cy="50"
            r="40"
            fill="transparent"
            stroke="currentColor"
            stroke-width="12"
            class="text-gray-50 dark:text-gray-900/50"
          />
          
          <!-- Slices -->
          <circle
            v-for="(slice, index) in slices"
            :key="index"
            cx="50"
            cy="50"
            r="40"
            fill="transparent"
            :stroke="slice.color"
            stroke-width="12"
            :stroke-dasharray="slice.strokeDasharray"
            :style="{ strokeDashoffset: slice.strokeDashoffset }"
            class="transition-all duration-1000 ease-out cursor-pointer hover:stroke-[14px]"
            @mouseenter="activePool = slice.name"
            @mouseleave="activePool = null"
          />
        </svg>
        
        <!-- Center Info -->
        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
          <template v-if="activePool">
            <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ activePool }}</span>
            <span class="text-xl font-bold text-gray-900 dark:text-white">{{ getActiveShare }}%</span>
          </template>
          <template v-else>
            <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</span>
            <span class="text-xl font-bold text-gray-900 dark:text-white">100%</span>
          </template>
        </div>
      </div>

      <!-- Legend -->
      <div class="flex flex-col space-y-3 w-full md:w-64 px-4">
        <div 
          v-for="stat in stats" 
          :key="stat.poolId" 
          class="flex items-center justify-between text-sm group cursor-default"
          @mouseenter="activePool = stat.name"
          @mouseleave="activePool = null"
        >
          <div class="flex items-center space-x-3 truncate">
            <div 
              class="w-3 h-3 rounded-full flex-shrink-0 transition-transform group-hover:scale-125" 
              :style="{ backgroundColor: getColor(stat.name) }"
            ></div>
            <span 
              class="font-medium truncate transition-colors" 
              :class="activePool === stat.name ? 'text-green-600 dark:text-green-400' : 'text-gray-700 dark:text-gray-300'"
            >
              {{ stat.name }}
            </span>
          </div>
          <div class="flex items-center space-x-2">
            <span class="text-gray-500 dark:text-gray-400 font-mono">{{ (stat.share * 100).toFixed(1) }}%</span>
          </div>
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
const activePool = ref(null)

const poolColors = {
  'ViaBTC': '#2ab1e2',
  'F2Pool': '#345ced',
  'AntPool': '#edae34',
  'KuPool': '#24ae8f',
  '00Hash': '#6366f1',
  'Binance Pool': '#f3ba2f',
  'EMCD': '#ff4d4d',
  'Mining-Dutch': '#ff8c00',
  'TrustPool': '#00c8ff',
  'LitecoinPool': '#345ced',
  'CloverPool': '#4ade80',
  'Unknown': '#9ca3af'
}

const getColor = (name) => poolColors[name] || '#cbd5e1'

const getActiveShare = computed(() => {
  if (!activePool.value) return 0
  const stat = stats.value.find(s => s.name === activePool.value)
  return stat ? (stat.share * 100).toFixed(1) : 0
})

const slices = computed(() => {
  let accumulatedPercent = 0
  // SVG circumference for r=40 is 2 * PI * 40 ≈ 251.327
  const circumference = 2 * Math.PI * 40
  
  return stats.value.map(stat => {
    const percent = stat.share
    const dashLength = percent * circumference
    const startOffset = accumulatedPercent * circumference
    accumulatedPercent += percent
    
    return {
      name: stat.name,
      color: getColor(stat.name),
      strokeDasharray: `${dashLength} ${circumference}`,
      strokeDashoffset: -startOffset
    }
  })
})

const fetchStats = async () => {
  try {
    const response = await fetch(props.apiUrl)
    const data = await response.json()
    // Group small pools into "Others" if we have too many
    if (data.length > 10) {
        const top = data.slice(0, 9)
        const others = data.slice(9)
        const othersShare = others.reduce((acc, s) => acc + s.share, 0)
        top.push({
            poolId: 999,
            name: 'Others',
            share: othersShare
        })
        stats.value = top
    } else {
        stats.value = data
    }
  } catch (err) {
    console.error('Failed to fetch mining pool stats:', err)
  } finally {
    loading.value = false
  }
}

onMounted(fetchStats)
</script>

<style scoped>
circle {
  transition: stroke-dasharray 0.8s cubic-bezier(0.4, 0, 0.2, 1), 
              stroke-dashoffset 0.8s cubic-bezier(0.4, 0, 0.2, 1),
              stroke-width 0.2s ease;
}
</style>
