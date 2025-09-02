<template>
  <TransitionGroup
    name="mempool"
    tag="div"
    class="mempool-list"
  >
    <a
      v-for="txid in sortedTxids"
      :key="txid"
      :href="`/tx/${txid}`"
      class="block px-6 py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
      :class="{ 'opacity-70': confirmed[txid] }"
    >
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
            <template v-if="confirmed[txid]">
              <!-- bi-check-circle-fill inline -->
              <svg class="w-4 h-4 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
              </svg>
            </template>
            <template v-else>
              <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </template>
          </div>
        </div>
        <div class="ml-4 min-w-0 flex-1">
          <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
            {{ txid.substring(0, 16) }}...{{ txid.substring(txid.length - 8) }}
          </p>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ confirmed[txid] ? 'Confirmed' : 'Unconfirmed' }}
          </p>
        </div>
      </div>
    </a>
  </TransitionGroup>
  <div v-if="sortedTxids.length === 0" class="px-6 py-8 text-center">
    <p class="text-gray-500 dark:text-gray-400">no transactions in mempool</p>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  initialTxids: {
    type: Array,
    default: () => []
  },
  apiUrl: {
    type: String,
    required: true
  },
  intervalMs: {
    type: Number,
    default: 10000
  }
})

// Reactive state
const txids = ref([...props.initialTxids])
const confirmed = ref({})
const firstSeen = ref({})
const lastCount = ref(props.initialTxids.length)

// Computed
const sortedTxids = computed(() => {
  return [...txids.value].sort((a, b) => (firstSeen.value[b] || 0) - (firstSeen.value[a] || 0))
})

// Internal state
let timer = null
let inFlight = false
let controller = null

// Methods
const updateCount = () => {
  const el = document.getElementById('mempool-count')
  if (el) el.innerText = (lastCount.value || 0).toLocaleString('en-US')
}

const fetchTxids = async () => {
  if (inFlight) return
  inFlight = true
  
  if (controller) {
    try { controller.abort() } catch (_) {}
  }
  controller = new AbortController()
  
  try {
    const response = await fetch(props.apiUrl, { signal: controller.signal })
    if (!response.ok) return
    
    const data = await response.json()
    if (!Array.isArray(data)) return
    
    lastCount.value = data.length
    
    const existing = new Set(txids.value)
    const newOnes = data.filter(txid => !existing.has(txid))
    const removed = txids.value.filter(id => !data.includes(id))
    
    // Mark removed as confirmed and schedule removal after 10 seconds
    for (const id of removed) {
      if (!confirmed.value[id]) {
        confirmed.value[id] = true
        setTimeout(() => {
          txids.value = txids.value.filter(txid => txid !== id)
          delete confirmed.value[id]
          delete firstSeen.value[id]
        }, 10000)
      }
    }
    
    // Add new ones with timestamps
    const now = Date.now()
    for (let i = 0; i < newOnes.length; i++) {
      const id = newOnes[i]
      if (!firstSeen.value[id]) {
        firstSeen.value[id] = now + (newOnes.length - i)
      }
    }
    
    // Add new transactions to the beginning of the array
    if (newOnes.length > 0) {
      txids.value = [...newOnes, ...txids.value]
    }
    const seen = new Set()
    txids.value = txids.value
      .filter(t => seen.has(t) ? false : (seen.add(t), true))
      .slice(0, 200)
    
    updateCount()
  } catch (err) {
    if (err?.name !== 'AbortError') {
      console.error('Error fetching mempool txids:', err)
    }
  } finally {
    inFlight = false
    controller = null
  }
}

const startPolling = () => {
  fetchTxids()
  if (!timer) {
    timer = setInterval(fetchTxids, props.intervalMs)
  }
}

const stopPolling = () => {
  if (timer) {
    clearInterval(timer)
    timer = null
  }
}

const handleVisibilityChange = () => {
  if (document.visibilityState === 'visible') {
    startPolling()
  } else {
    stopPolling()
  }
}

// Lifecycle
onMounted(() => {
  // Initialize firstSeen for initial txids
  const now = Date.now()
  for (let i = 0; i < txids.value.length; i++) {
    const id = txids.value[i]
    if (!firstSeen.value[id]) {
      // Assign larger timestamps to earlier items to preserve current visual order
      firstSeen.value[id] = now + (txids.value.length - i)
    }
  }
  
  updateCount()
  startPolling()
  document.addEventListener('visibilitychange', handleVisibilityChange)
})

onUnmounted(() => {
  stopPolling()
  document.removeEventListener('visibilitychange', handleVisibilityChange)
  if (controller) {
    try { controller.abort() } catch (_) {}
  }
})
</script>

<style scoped>
.mempool-enter-active {
  transition: all 0.4s ease;
}

.mempool-leave-active {
  transition: all 1.5s ease;
}

.mempool-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.mempool-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.mempool-move {
  transition: transform 0.4s ease;
}
</style>
