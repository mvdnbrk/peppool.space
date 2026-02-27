<template>
  <div class="w-full flex flex-col">
    <!-- Header with Toggle -->
    <div class="flex items-center justify-between mb-8">
      <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Hashrate History</h2>
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

    <div v-if="isLoading" class="flex flex-col items-center justify-center h-64 text-gray-400">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500 mb-4"></div>
      <span>Loading pool statistics...</span>
    </div>
    <div v-else-if="error" class="flex items-center justify-center h-64 text-red-500 bg-red-50 dark:bg-red-900/10 rounded-lg">
      <div class="text-center">
        <p class="font-bold mb-1">Failed to load chart</p>
        <p class="text-sm opacity-75">{{ error }}</p>
      </div>
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
        lineColor: '#15803d',   // green-700
        topColor: 'rgba(21, 128, 61, 0.4)',
        bottomColor: 'rgba(21, 128, 61, 0.0)',
      }
    : {
        background: '#f9fafb',
        textColor: '#111827',   // gray-900
        gridColor: '#e5e7eb',   // gray-200
        lineColor: '#15803d',   // green-700
        topColor: 'rgba(21, 128, 61, 0.2)',
        bottomColor: 'rgba(21, 128, 61, 0.0)',
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

const initChart = async (data) => {
  if (!chartContainer.value) return
  
  const theme = getTheme()
  const lib = await loadCdnLibrary()
  
  if (!lib || typeof lib.createChart !== 'function') {
    throw new Error('Chart library failed to load')
  }
  
  chart = lib.createChart(chartContainer.value, {
    width: chartContainer.value.clientWidth || 600,
    height: 400,
    layout: { 
      background: { color: theme.background }, 
      textColor: theme.textColor,
      fontFamily: 'Inter, system-ui, sans-serif',
    },
    grid: { 
      vertLines: { visible: false }, 
      horzLines: { color: theme.gridColor, style: 2 } 
    },
    timeScale: { 
      borderVisible: false,
      timeVisible: true, 
      secondsVisible: false,
      rightOffset: 0,
      fixLeftEdge: true,
      fixRightEdge: true,
    },
    rightPriceScale: { 
      borderVisible: false,
      scaleMargins: { top: 0.1, bottom: 0.1 },
    },
    handleScroll: { mouseWheel: true, pressedMouseMove: true },
    handleScale: { axisPressedMouseMove: true, mouseWheel: true, pinch: true },
  })
  
  series = chart.addAreaSeries({
    lineColor: theme.lineColor,
    topColor: theme.topColor,
    bottomColor: theme.bottomColor,
    lineWidth: 3,
    priceFormat: {
      type: 'custom',
      formatter: formatHashrate,
    },
  })

  series.setData(data)
  
  requestAnimationFrame(() => {
    handleResize()
    chart.timeScale().fitContent()
  })
}

const updateDisplay = (latestStat) => {
  if (!latestStat) return
  
  const shareEl = document.getElementById('pool-share-display')
  if (shareEl) {
    shareEl.textContent = (latestStat.share * 100).toFixed(2) + '%'
  }
  
  const hashrateEl = document.getElementById('pool-hashrate-display')
  if (hashrateEl) {
    hashrateEl.textContent = formatHashrate(latestStat.hashrate)
  }
}

const fetchData = async () => {
  try {
    isLoading.value = true
    
    const url = new URL(props.apiUrl, window.location.origin)
    url.searchParams.append('type', type.value)
    
    const res = await fetch(url.toString())
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    const json = await res.json()
    
    updateDisplay(json.latestStat)
    
    if (json.hashrateHistory && json.hashrateHistory.length > 0) {
      await nextTick()
      if (!chart) {
        await initChart(json.hashrateHistory)
      } else {
        series.setData(json.hashrateHistory)
        requestAnimationFrame(() => {
          handleResize()
          chart.timeScale().fitContent()
        })
      }
    } else {
      error.value = "No hashrate history available for this pool yet."
    }
  } catch (e) {
    error.value = e.message
    console.error('PoolHashrateChart error:', e)
  } finally {
    isLoading.value = false
  }
}

const handleResize = () => {
  if (chart && chartContainer.value) {
    const width = chartContainer.value.clientWidth
    if (width > 0) {
      chart.resize(width, 400)
    }
  }
}

let resizeObserver

onMounted(() => {
  fetchData()
  window.addEventListener('resize', handleResize)
  
  if (window.ResizeObserver) {
    resizeObserver = new ResizeObserver(() => {
      handleResize()
    })
    if (chartContainer.value) {
      resizeObserver.observe(chartContainer.value)
    }
  }
  
  // Watch for theme changes
  const observer = new MutationObserver(() => {
    if (chart) {
      const theme = getTheme()
      chart.applyOptions({
        layout: { background: { color: theme.background }, textColor: theme.textColor },
        grid: { horzLines: { color: theme.gridColor } },
      })
      if (series) {
        series.applyOptions({
          lineColor: theme.lineColor,
          topColor: theme.topColor,
          bottomColor: theme.bottomColor,
        })
      }
    }
  })
  
  observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] })
})

watch(type, fetchData)

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
  if (resizeObserver) {
    resizeObserver.disconnect()
  }
  if (chart) {
    chart.remove()
    chart = null
  }
  series = null
})
</script>

<style scoped>
.lw-chart { width: 100%; height: 400px; }
</style>
