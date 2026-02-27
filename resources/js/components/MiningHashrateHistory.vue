<template>
  <div class="flex flex-col">
    <!-- Header with Toggle -->
    <div class="flex items-center justify-between mb-8">
      <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Network Hashrate History</h2>
      <div class="flex p-1 bg-gray-100 dark:bg-gray-900/50 rounded-lg">
        <button
          @click="type = 'daily'"
          class="px-3 py-1 text-xs font-medium rounded-md transition-all duration-200 cursor-pointer"
          :class="type === 'daily' ? 'bg-white dark:bg-gray-800 text-green-700 dark:text-green-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
        >
          Daily
        </button>
        <button
          @click="type = 'weekly'"
          class="px-3 py-1 text-xs font-medium rounded-md transition-all duration-200 cursor-pointer"
          :class="type === 'weekly' ? 'bg-white dark:bg-gray-800 text-green-700 dark:text-green-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
        >
          Weekly
        </button>
      </div>
    </div>

    <div v-if="isLoading" class="flex items-center justify-center h-[400px] text-gray-400">
      <div class="animate-pulse">Loading hashrate history…</div>
    </div>
    <div v-else-if="error" class="flex items-center justify-center h-[400px] text-red-500">
      <span>{{ error }}</span>
    </div>
    <div v-show="!isLoading && !error" ref="chartContainer" class="lw-chart"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue'

const props = defineProps({
  apiUrl: { type: String, required: true },
})

let chart
let series
const chartContainer = ref(null)

const isLoading = ref(true)
const firstLoad = ref(true)
const error = ref(null)
const type = ref('daily')

const getTheme = () => {
  const isDark = document.documentElement.classList.contains('dark')
    || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)

  return isDark
    ? {
        background: '#0f172a', // slate-900
        textColor: '#e2e8f0',   // slate-200
        gridColor: '#1f2937',   // gray-800
        seriesColor: '#15803d', // green-700
      }
    : {
        background: '#f9fafb',
        textColor: '#111827',   // gray-900
        gridColor: '#e5e7eb',   // gray-200
        seriesColor: '#15803d', // green-700
      }
}

const formatHashrate = (value) => {
  if (value === 0) return '0 H/s'
  const units = ['H/s', 'KH/s', 'MH/s', 'GH/s', 'TH/s', 'PH/s', 'EH/s']
  const i = Math.floor(Math.log(value) / Math.log(1000))
  return (value / Math.pow(1000, i)).toFixed(2) + ' ' + units[i]
}

const loadCdnLibrary = () => new Promise((resolve, reject) => {
  if (typeof window === 'undefined') return reject(new Error('no-window'))
  const existing = document.querySelector('script[data-lwcharts-cdn="true"]')
  if (existing) {
    if (existing.getAttribute('data-loaded') === 'true') return resolve(window.LightweightCharts)
    existing.addEventListener('load', () => resolve(window.LightweightCharts))
    existing.addEventListener('error', reject)
    return
  }
  const s = document.createElement('script')
  s.setAttribute('data-lwcharts-cdn', 'true')
  s.src = 'https://unpkg.com/lightweight-charts@4.2.0/dist/lightweight-charts.standalone.production.js'
  s.async = true
  s.onload = () => {
    s.setAttribute('data-loaded', 'true')
    resolve(window.LightweightCharts)
  }
  s.onerror = reject
  document.head.appendChild(s)
})

const initChart = async () => {
  if (!chartContainer.value) return
  await nextTick()
  const width = chartContainer.value.clientWidth
  const theme = getTheme()
  const lib = await loadCdnLibrary()
  if (!lib || typeof lib.createChart !== 'function') {
    error.value = 'Chart library failed to load'
    return
  }
  
  chart = lib.createChart(chartContainer.value, {
    width: width || 600,
    height: 400,
    layout: { background: { color: theme.background }, textColor: theme.textColor },
    grid: { vertLines: { visible: false }, horzLines: { color: theme.gridColor } },
    timeScale: { 
        timeVisible: true, 
        secondsVisible: false,
        rightOffset: 0,
        fixLeftEdge: true,
        fixRightEdge: true,
    },
    rightPriceScale: { 
        scaleMargins: { top: 0.1, bottom: 0.1 },
        borderVisible: false,
    },
  })
  
  series = chart.addAreaSeries({
    lineColor: theme.seriesColor,
    topColor: theme.seriesColor + '80', // Add transparency
    bottomColor: theme.seriesColor + '00',
    lineWidth: 2,
    priceFormat: {
      type: 'custom',
      formatter: formatHashrate,
    },
  })
}

const handleResize = () => {
  if (chart && chartContainer.value) {
    const width = chartContainer.value.clientWidth
    if (width > 0) {
      chart.resize(width, 400)
    }
  }
}

const fetchSeries = async () => {
  if (!props.apiUrl) return
  try {
    if (firstLoad.value) isLoading.value = true
    
    const url = new URL(props.apiUrl, window.location.origin)
    url.searchParams.append('type', type.value)
    
    const res = await fetch(url.toString())
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    const json = await res.json()
    
    if (!series) throw new Error('Series not ready')
    
    const normalized = json.map(p => ({
      time: Math.floor(new Date(p.timestamp).getTime() / 1000),
      value: Number(p.totalHashrate),
    }))
    
    series.setData(normalized)
    
    // Ensure the chart fits the container and data
    requestAnimationFrame(() => {
      handleResize()
      if (chart) {
        chart.timeScale().fitContent()
      }
    })
  } catch (e) {
    error.value = e.message
  } finally {
    isLoading.value = false
    firstLoad.value = false
  }
}

let resizeObserver

onMounted(async () => {
  await initChart()
  await fetchSeries()
  window.addEventListener('resize', handleResize)
  
  if (window.ResizeObserver) {
    resizeObserver = new ResizeObserver(() => {
      handleResize()
    })
    if (chartContainer.value) {
      resizeObserver.observe(chartContainer.value)
    }
  }

  // Theme observer
  const themeObserver = new MutationObserver(() => {
    if (chart) {
      const theme = getTheme()
      chart.applyOptions({
        layout: { background: { color: theme.background }, textColor: theme.textColor },
        grid: { horzLines: { color: theme.gridColor } },
      })
      if (series) {
        series.applyOptions({
          lineColor: theme.seriesColor,
          topColor: theme.seriesColor + '80',
        })
      }
    }
  })
  themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] })
})

watch(type, fetchSeries)

onUnmounted(() => {
  if (chart) { chart.remove(); chart = null }
  series = null
  window.removeEventListener('resize', handleResize)
  if (resizeObserver) resizeObserver.disconnect()
})
</script>

<style scoped>
.lw-chart { width: 100%; height: 400px; }
</style>
