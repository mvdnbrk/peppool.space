<template>
  <span id="pepecoin-price">${{ formattedPrice }}</span>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'

const props = defineProps({
  initialPrice: {
    type: [Number, String],
    required: true
  },
  apiUrl: {
    type: String,
    default: '/api/prices'
  },
  intervalMs: {
    type: Number,
    default: 300000 // 5 minutes
  }
})

const price = ref(Number(props.initialPrice))
const formattedPrice = computed(() => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 8,
    maximumFractionDigits: 8
  }).format(price.value)
})

let timer = null
let isVisible = document.visibilityState === 'visible'

const fetchPrice = async () => {
  if (!isVisible) return
  
  try {
    const res = await fetch(props.apiUrl)
    if (!res.ok) throw new Error('Network response was not ok')
    const data = await res.json()
    if (data && data.USD) {
      price.value = data.USD
    }
  } catch (e) {
    console.error('Error fetching Pepecoin price:', e)
  }
}

const managePolling = () => {
  if (timer) clearInterval(timer)
  if (isVisible) {
    timer = setInterval(fetchPrice, props.intervalMs)
  }
}

const handleVisibilityChange = () => {
  isVisible = document.visibilityState === 'visible'
  if (isVisible) {
    fetchPrice()
  }
  managePolling()
}

onMounted(() => {
  document.addEventListener('visibilitychange', handleVisibilityChange)
  managePolling()
})

onUnmounted(() => {
  document.removeEventListener('visibilitychange', handleVisibilityChange)
  if (timer) clearInterval(timer)
})
</script>
