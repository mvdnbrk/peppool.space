<template>
  <div>
    <div v-if="isLoading" class="flex items-center justify-center h-64 text-gray-400">
      <span>Loading chart…</span>
    </div>
    <div v-else-if="error" class="flex items-center justify-center h-64 text-red-500">
      <span>{{ error }}</span>
    </div>
    <div ref="chartContainer" class="lw-chart"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'

const props = withDefaults(defineProps({
  apiUrl: { type: String, required: true },
  // Optional: override refresh using seconds. If > 0, it takes precedence over refreshMs.
  refreshSeconds: { type: Number, default: 0 },
  // Refresh cadence in ms (default 5 minutes) used when refreshSeconds <= 0
  refreshMs: { type: Number, default: 5 * 60 * 1000 },
}), {})

let chart
let series
const chartContainer = ref(null)

const isLoading = ref(false)
const error = ref(null)
let intervalId = null

// THEME: compute colors based on dark mode
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

let mql = null
let htmlObserver = null

// Format price values without trailing zeros (up to 8 decimals)
const formatPrice = (value) => {
  const fixed = Number(value).toFixed(8)
  return fixed
    .replace(/(\.\d*[1-9])0+$/, '$1') // trim trailing zeros after last non-zero decimal
    .replace(/\.0+$/, '')              // trim .0... to integer
}

const loadCdnLibrary = () => new Promise((resolve, reject) => {
  if (typeof window === 'undefined') return reject(new Error('no-window'))
  // Force-load pinned 4.x even if a different global already exists
  const existing = document.querySelector('script[data-lwcharts-cdn="true"]')
  if (existing) {
    existing.addEventListener('load', () => resolve(window.LightweightCharts))
    existing.addEventListener('error', reject)
    // If already loaded, resolve immediately
    if (existing.getAttribute('data-loaded') === 'true') return resolve(window.LightweightCharts)
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
  // Ensure container has layout before creating chart
  await nextTick()
  const width = chartContainer.value.clientWidth
  const height = chartContainer.value.clientHeight || 400
  const theme = getTheme()
  // Always use CDN global to avoid ESM interop issues in dev
  const lib = await loadCdnLibrary()
  if (!lib || typeof lib.createChart !== 'function') {
    error.value = 'Chart library failed to load'
    return
  }
  let c = lib.createChart(chartContainer.value, {
    width: width || 600,
    height,
    layout: { background: { color: theme.background }, textColor: theme.textColor },
    grid: { vertLines: { visible: false }, horzLines: { color: theme.gridColor } },
    timeScale: { timeVisible: true, secondsVisible: false },
    rightPriceScale: { scaleMargins: { top: 0.1, bottom: 0.1 } },
  })
  if (!c || typeof c.addLineSeries !== 'function') {
    error.value = 'Chart initialization error'
    return
  }
  chart = c
  series = chart.addLineSeries({
    color: theme.seriesColor,
    lineWidth: 2,
    priceFormat: {
      type: 'custom',
      minMove: 0.00000001,
      formatter: formatPrice,
    },
  })
}

const resizeHandler = () => {
  if (!chart || !chartContainer.value) return
  const rect = chartContainer.value.getBoundingClientRect()
  chart.resize(rect.width, rect.height)
}

const fetchSeries = async () => {
  if (!props.apiUrl) return
  try {
    isLoading.value = true
    const res = await fetch(props.apiUrl, { headers: { 'Accept': 'application/json' } })
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    const json = await res.json()
    if (!Array.isArray(json.series)) throw new Error('Invalid payload')
    if (!series) throw new Error('Series not ready')
    const normalized = json.series.map(p => ({
      time: Number(p.time),
      value: Number(p.value),
    }))
    series.setData(normalized)
    if (chart && typeof chart.timeScale === 'function') {
      chart.timeScale().fitContent()
    }
  } catch (e) {
    error.value = e.message
  } finally {
    isLoading.value = false
  }
}

const applyThemeToChart = () => {
  if (!chart) return
  const theme = getTheme()
  chart.applyOptions({
    layout: { background: { color: theme.background }, textColor: theme.textColor },
    grid: { vertLines: { visible: false }, horzLines: { color: theme.gridColor } },
  })
  if (series) series.applyOptions({ color: theme.seriesColor })
}

onMounted(async () => {
  await initChart()
  await fetchSeries()
  const intervalMs = (typeof props.refreshSeconds === 'number' && props.refreshSeconds > 0)
    ? props.refreshSeconds * 1000
    : props.refreshMs
  intervalId = setInterval(fetchSeries, intervalMs)
  window.addEventListener('resize', resizeHandler)

  // Theme listeners
  htmlObserver = new MutationObserver(() => applyThemeToChart())
  htmlObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] })
  if (window.matchMedia) {
    mql = window.matchMedia('(prefers-color-scheme: dark)')
    if (typeof mql.addEventListener === 'function') mql.addEventListener('change', applyThemeToChart)
    else if (typeof mql.addListener === 'function') mql.addListener(applyThemeToChart)
  }
})

onUnmounted(() => {
  if (intervalId) { clearInterval(intervalId); intervalId = null }
  if (chart) { chart.remove(); chart = null }
  series = null
  window.removeEventListener('resize', resizeHandler)
  if (htmlObserver) { htmlObserver.disconnect(); htmlObserver = null }
  if (mql) {
    if (typeof mql.removeEventListener === 'function') mql.removeEventListener('change', applyThemeToChart)
    else if (typeof mql.removeListener === 'function') mql.removeListener(applyThemeToChart)
    mql = null
  }
})
</script>

<style scoped>
.lw-chart { width: 100%; height: 400px; }
</style>
