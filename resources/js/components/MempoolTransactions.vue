<template>
  <TransitionGroup
    name="mempool"
    tag="div"
    class="mempool-list"
  >
    <a
      v-for="tx in sortedTransactions"
      :key="tx.txid"
      :href="txRoute.replace('__TXID__', tx.txid)"
      class="block px-6 py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
      :class="{ 'opacity-70': confirmed[tx.txid] }"
    >
      <div class="flex items-center justify-between">
        <div class="flex items-center min-w-0 flex-1">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
              <template v-if="confirmed[tx.txid]">
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
              {{ tx.txid.substring(0, 16) }}...{{ tx.txid.substring(tx.txid.length - 8) }}
            </p>
          </div>
        </div>
        <div class="text-right ml-4">
          <p class="text-sm font-semibold text-gray-900 dark:text-white">
            <span>{{ splitAmount(tx.value / 100000000).whole }}</span><span class="text-[0.85em] text-gray-500 dark:text-gray-400 hidden sm:inline">{{ splitAmount(tx.value / 100000000).decimal }}</span>
            PEPE
          </p>
        </div>
      </div>
    </a>
  </TransitionGroup>
  <div v-if="placeholder" class="px-6 py-8 flex flex-col items-center justify-center space-y-3">
    <div v-if="placeholder.spin" class="w-8 h-8 border-2 border-green-600 border-t-transparent rounded-full animate-spin"></div>
    <p class="text-sm text-gray-500 dark:text-gray-400">{{ placeholder.text }}</p>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'

const props = defineProps({
  initialTransactions: {
    type: Array,
    default: () => []
  },
  apiUrl: {
    type: String,
    required: true
  },
  txRoute: {
    type: String,
    default: ''
  }
})

// Reactive state
const transactions = ref([...props.initialTransactions])
const confirmed = ref({})
const firstSeen = ref({})
const loading = ref(props.initialTransactions.length === 0)
const showEmpty = ref(false)

// Computed
const sortedTransactions = computed(() => {
  return [...transactions.value].sort((a, b) => (firstSeen.value[b.txid] || 0) - (firstSeen.value[a.txid] || 0))
})

const placeholder = computed(() => {
  if (loading.value) {
    return { text: 'fetching mempool...', spin: true }
  }

  if (showEmpty.value && transactions.value.length === 0) {
    return { text: 'no transactions in mempool', spin: false }
  }

  return null
})

// Internal state
let inFlight = false
let controller = null
let emptyMessageTimer = null

// Methods
const formatAmount = (amount) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 8
  }).format(amount)
}

const splitAmount = (amount) => {
  const formatted = formatAmount(amount);
  const parts = formatted.split('.');
  return {
    whole: parts[0],
    decimal: parts[1] ? '.' + parts[1] : ''
  };
}

const fetchTransactions = async () => {
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
    
    loading.value = false
    
    const existingIds = new Set(transactions.value.map(t => t.txid))
    const newDataIds = new Set(data.map(t => t.txid))
    
    const newOnes = data.filter(t => !existingIds.has(t.txid))
    const removed = transactions.value.filter(t => !newDataIds.has(t.txid))
    
    // Mark removed as confirmed and schedule removal after 10 seconds
    for (const tx of removed) {
      if (!confirmed.value[tx.txid]) {
        confirmed.value[tx.txid] = true
        setTimeout(() => {
          transactions.value = transactions.value.filter(t => t.txid !== tx.txid)
          delete confirmed.value[tx.txid]
          delete firstSeen.value[tx.txid]
        }, 3000)
      }
    }
    
    // Add new ones with timestamps
    const now = Date.now()
    for (let i = 0; i < newOnes.length; i++) {
      const tx = newOnes[i]
      if (!firstSeen.value[tx.txid]) {
        firstSeen.value[tx.txid] = now + (newOnes.length - i)
      }
    }
    
    if (newOnes.length > 0) {
      transactions.value = [...newOnes, ...transactions.value]
    }
    
    const seen = new Set()
    transactions.value = transactions.value
      .filter(t => seen.has(t.txid) ? false : (seen.add(t.txid), true))
      .slice(0, 200)
    
  } catch (err) {
    if (err?.name !== 'AbortError') {
      console.error('Error fetching mempool transactions:', err)
    }
  } finally {
    inFlight = false
    controller = null
  }
}

watch([transactions, loading], ([list, isLoading]) => {
  clearTimeout(emptyMessageTimer)
  
  const isNowEmpty = list.length === 0 && !isLoading
  
  isNowEmpty
    ? (emptyMessageTimer = setTimeout(() => showEmpty.value = true, 1600))
    : (showEmpty.value = false)
}, { immediate: true })

onMounted(() => {
  const now = Date.now()
  for (let i = 0; i < transactions.value.length; i++) {
    const tx = transactions.value[i]
    if (!firstSeen.value[tx.txid]) {
      firstSeen.value[tx.txid] = now + (transactions.value.length - i)
    }
  }
  
  fetchTransactions()
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
      fetchTransactions()
    }
  })
  window.addEventListener('mempool-updated', fetchTransactions)
})

onUnmounted(() => {
  document.removeEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
      fetchTransactions()
    }
  })
  window.removeEventListener('mempool-updated', fetchTransactions)
  if (controller) {
    try { controller.abort() } catch (_) {}
  }
  if (emptyMessageTimer) {
    clearTimeout(emptyMessageTimer)
    emptyMessageTimer = null
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
