<template>
  <div>
    <div v-if="isLoading" class="flex items-center justify-center h-64 text-gray-400">
      <span>Loading hashrate history…</span>
    </div>
    <div v-else-if="error" class="flex items-center justify-center h-64 text-red-500">
      <span>{{ error }}</span>
    </div>
    <div ref="chartContainer" class="lw-chart min-h-[300px]"></div>
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

const isLoading = ref(false)
const firstLoad = ref(true)
const error = ref(null)

const getTheme = () => {
  const isDark = document.documentElement.classList.contains('dark')
    || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)

  return isDark
    ? {
        background: '#1f2937', // gray-800
        textColor: '#e2e8f0',   // slate-200
        gridColor: '#374151',   // gray-700
        seriesColor: '#06b6d4', // cyan-500
      }
    : {
        background: '#ffffff',
        textColor: '#111827',   // gray-900
        gridColor: '#f3f4f6',   // gray-100
        seriesColor: '#0891b2', // cyan-600
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
  const height = chartContainer.value.clientHeight || 300
  const theme = getTheme()
  const lib = await loadCdnLibrary()
  if (!lib || typeof lib.createChart !== 'function') {
    error.value = 'Chart library failed to load'
    return
  }
  
  chart = lib.createChart(chartContainer.value, {
    width: width || 600,
    height,
    layout: { background: { color: theme.background }, textColor: theme.textColor },
    grid: { vertLines: { visible: false }, horzLines: { color: theme.gridColor } },
    timeScale: { timeVisible: true, secondsVisible: false },
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

const fetchSeries = async () => {
  if (!props.apiUrl) return
  try {
    if (firstLoad.value) isLoading.value = true
    const res = await fetch(props.apiUrl)
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    const json = await res.json()
    
    if (!series) throw new Error('Series not ready')
    
    const normalized = json.map(p => ({
      time: Math.floor(new Date(p.timestamp).getTime() / 1000),
      value: Number(p.totalHashrate),
    }))
    
    series.setData(normalized)
    if (firstLoad.value && chart) {
      chart.timeScale().fitContent()
    }
  } catch (e) {
    error.value = e.message
  } finally {
    isLoading.value = false
    firstLoad.value = false
  }
}

onMounted(async () => {
  await initChart()
  await fetchSeries()
  window.addEventListener('resize', () => {
    if (chart && chartContainer.value) {
      chart.resize(chartContainer.value.clientWidth, chartContainer.value.clientHeight)
    }
  })
})

onUnmounted(() => {
  if (chart) { chart.remove(); chart = null }
  series = null
})
</script>

<style scoped>
.lw-chart { width: 100%; }
</style>
