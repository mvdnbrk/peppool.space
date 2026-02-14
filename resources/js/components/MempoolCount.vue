<template>
  <span><span id="mempool-count">{{ formattedCount }}</span> txs</span>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'

const props = defineProps({
  initialCount: {
    type: [Number, String],
    required: true
  },
  apiUrl: {
    type: String,
    default: '/api/mempool'
  },
  intervalMs: {
    type: Number,
    default: 10000 // 10 seconds
  }
})

const count = ref(Number(props.initialCount))
const formattedCount = computed(() => {
  return new Intl.NumberFormat('en-US').format(count.value)
})

let timer = null
let isVisible = document.visibilityState === 'visible'

const fetchCount = async () => {
  if (!isVisible) return
  
  try {
    const res = await fetch(props.apiUrl)
    if (!res.ok) throw new Error('Network response was not ok')
    const data = await res.json()
    if (data && typeof data.count !== 'undefined') {
      const prevCount = count.value
      count.value = data.count
      
      if (prevCount !== count.value) {
        window.dispatchEvent(new CustomEvent('mempool-updated', {
          detail: { count: count.value }
        }))
      }
    }
  } catch (e) {
    console.error('Error fetching mempool count:', e)
  }
}

const managePolling = () => {
  if (timer) clearInterval(timer)
  if (isVisible) {
    timer = setInterval(fetchCount, props.intervalMs)
  }
}

const handleVisibilityChange = () => {
  isVisible = document.visibilityState === 'visible'
  if (isVisible) {
    fetchCount()
  }
  managePolling()
}

onMounted(() => {
  document.addEventListener('visibilitychange', handleVisibilityChange)
  managePolling()
  
  // Also listen for updates from the MempoolTransactions component
  // although that one only sees the last 10, it might be useful if we
  // ever make it smarter. For now, the dedicated polling is better.
  window.addEventListener('mempool-count-updated', (event) => {
    if (event.detail && typeof event.detail.count !== 'undefined') {
      count.value = Number(event.detail.count)
    }
  })
})

onUnmounted(() => {
  document.removeEventListener('visibilitychange', handleVisibilityChange)
  if (timer) clearInterval(timer)
})
</script>
