<template>
  <span id="current-block-height">{{ formattedHeight }}</span>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'

const props = defineProps({
  initialHeight: {
    type: [Number, String],
    required: true
  },
  apiUrl: {
    type: String,
    default: '/api/blocks/tip/height'
  },
  intervalMs: {
    type: Number,
    default: 30000 // 30 seconds
  }
})

const height = ref(Number(props.initialHeight))
const formattedHeight = computed(() => {
  return new Intl.NumberFormat('en-US').format(height.value)
})

let timer = null
let isVisible = document.visibilityState === 'visible'

const fetchHeight = async () => {
  if (!isVisible) return
  
  try {
    const res = await fetch(props.apiUrl)
    if (!res.ok) throw new Error('Network response was not ok')
    const heightText = await res.text()
    const newHeight = parseInt(heightText, 10)
    if (!isNaN(newHeight)) {
      height.value = newHeight
    }
  } catch (e) {
    console.error('Error fetching block height:', e)
  }
}

const managePolling = () => {
  if (timer) clearInterval(timer)
  if (isVisible) {
    timer = setInterval(fetchHeight, props.intervalMs)
  }
}

const handleVisibilityChange = () => {
  isVisible = document.visibilityState === 'visible'
  if (isVisible) {
    fetchHeight()
  }
  managePolling()
}

onMounted(() => {
  document.addEventListener('visibilitychange', handleVisibilityChange)
  managePolling()
  
  // Listen for updates from LatestBlocks component if it exists
  window.addEventListener('block-height-updated', (event) => {
    if (event.detail && event.detail.height) {
      height.value = Number(event.detail.height)
    }
  })
})

onUnmounted(() => {
  document.removeEventListener('visibilitychange', handleVisibilityChange)
  if (timer) clearInterval(timer)
})
</script>
