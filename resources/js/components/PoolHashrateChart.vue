<template>
  <div class="w-full">
    <div v-if="isLoading" class="flex flex-col items-center justify-center h-64 text-gray-400">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mb-4"></div>
      <span>Loading pool statistics...</span>
    </div>
    <div v-else-if="error" class="flex items-center justify-center h-64 text-red-500 bg-red-50 dark:bg-red-900/10 rounded-lg">
      <div class="text-center">
        <p class="font-bold mb-1">Failed to load chart</p>
        <p class="text-sm opacity-75">{{ error }}</p>
      </div>
    </div>
    <div v-show="!isLoading && !error" ref="chartContainer" class="lw-chart min-h-[400px]"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'

const props = defineProps({
  apiUrl: { type: String, required: true },
})

let chart
let series
const chartContainer = ref(null)

const isLoading = ref(true)
const error = ref(null)

const getTheme = () => {
  const isDark = document.documentElement.classList.contains('dark')
  
  return isDark
    ? {
        background: 'transparent',
        textColor: '#9ca3af',   // gray-400
        gridColor: '#374151',   // gray-700
        lineColor: '#3b82f6',   // blue-500
        topColor: 'rgba(59, 130, 246, 0.4)',
        bottomColor: 'rgba(59, 130, 246, 0.0)',
      }
    : {
        background: 'transparent',
        textColor: '#6b7280',   // gray-500
        gridColor: '#f3f4f6',   // gray-100
        lineColor: '#2563eb',   // blue-600
        topColor: 'rgba(37, 99, 235, 0.2)',
        bottomColor: 'rgba(37, 99, 235, 0.0)',
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
    width: chartContainer.value.clientWidth,
    height: 400,
    layout: { 
      background: { type: 'solid', color: theme.background }, 
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
      secondsVisible: false 
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
  chart.timeScale().fitContent()
}

const updateDisplay = (latestStat) => {
  if (!latestStat) return
  
  const shareEl = document.getElementById('pool-share-display')
  if (shareEl) {
    shareEl.textContent = latestStat.share.toFixed(2) + '%'
  }
  
  const hashrateEl = document.getElementById('pool-hashrate-display')
  if (hashrateEl) {
    hashrateEl.textContent = formatHashrate(latestStat.hashrate)
  }
}

const fetchData = async () => {
  try {
    isLoading.value = true
    const res = await fetch(props.apiUrl)
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    const json = await res.json()
    
    updateDisplay(json.latestStat)
    
    if (json.hashrateHistory && json.hashrateHistory.length > 0) {
      await nextTick()
      await initChart(json.hashrateHistory)
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
    chart.applyOptions({ width: chartContainer.value.clientWidth })
  }
}

onMounted(() => {
  fetchData()
  window.addEventListener('resize', handleResize)
  
  // Watch for theme changes
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.attributeName === 'class' && chart) {
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
  })
  
  observer.observe(document.documentElement, { attributes: true })
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
  if (chart) {
    chart.remove()
    chart = null
  }
  series = null
})
</script>

<style scoped>
.lw-chart { width: 100%; }
</style>
