<template>
  <div class="space-y-6">
    <!-- Transaction Header -->
    <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6 mb-6 border border-gray-200 dark:border-gray-700">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Transaction Details</h1>
        <div class="flex items-center space-x-2">
          <span v-if="isCoinbase" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
            Coinbase
          </span>
          <span
            v-if="confirmed"
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300"
          >
            Confirmed
          </span>
          <span
            v-else
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300"
          >
            Unconfirmed
          </span>
        </div>
      </div>

      <div class="mb-4">
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Transaction ID</h3>
        <p class="text-sm font-mono bg-gray-100 dark:bg-gray-800 dark:text-gray-100 p-3 rounded break-all">{{ txid }}</p>
      </div>

      <div v-if="confirmed" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
        <div>
          <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Block Hash</h3>
          <p class="text-sm font-mono bg-gray-100 dark:bg-gray-800 dark:text-gray-100 p-2 rounded break-all">
            <a :href="'/block/' + blockHash" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
              {{ blockHash }}
            </a>
          </p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Block Height</h3>
          <p class="text-sm bg-gray-100 dark:bg-gray-800 dark:text-gray-100 p-2 rounded">
            <a :href="'/block/' + blockHeight" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
              {{ formatNumber(blockHeight) }}
            </a>
          </p>
        </div>
      </div>
    </div>

    <!-- Transaction Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
      <!-- Time/Received Card -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="ml-5">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ confirmed ? 'Time' : 'Received' }}</p>
            <div class="text-lg font-bold text-gray-900 dark:text-white">
              <timestamp
                v-if="time"
                :key="time"
                x-data="timestamp"
                :datetime="new Date(time * 1000).toISOString()"
                x-text="relativeTime"
                :title="new Date(time * 1000).toLocaleString()"
              ></timestamp>
              <span v-else>Loading...</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Confirmations Card -->
      <div v-if="confirmed" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="ml-5">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Confirmations</p>
            <div class="text-lg font-bold text-gray-900 dark:text-white">
              {{ formatNumber(confirmations) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Size Card -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
              </svg>
            </div>
          </div>
          <div class="ml-5">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Size</p>
            <div class="text-lg font-bold text-gray-900 dark:text-white">
              {{ formatNumber(size) }} bytes
            </div>
          </div>
        </div>
      </div>

      <!-- Fee Card -->
      <div v-if="!isCoinbase && fee > 0" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="ml-5">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Fee</p>
            <div class="text-lg font-bold text-gray-900 dark:text-white">
              <span class="inline-flex flex-row flex-nowrap items-baseline truncate max-w-full">
                <span class="shrink-0">{{ splitAmount(fee).whole }}</span><span class="text-[0.85em] text-gray-500 dark:text-gray-400 truncate shrink">{{ splitAmount(fee).decimal }}</span>
              </span>
              <span class="ml-1 text-sm font-normal">PEPE</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue'

const props = defineProps({
  txid: { type: String, required: true },
  isCoinbase: { type: Boolean, default: false },
  initialConfirmed: { type: Boolean, default: false },
  initialBlockHash: { type: String, default: '' },
  initialBlockHeight: { type: [Number, String], default: '' },
  initialBlockTime: { type: [Number, String], default: '' },
  initialConfirmations: { type: Number, default: 0 },
  size: { type: Number, default: 0 },
  fee: { type: Number, default: 0 },
  statusApiUrl: { type: String, required: true },
  tipHeightApiUrl: { type: String, required: true },
  initialBlockTipHeight: { type: Number, required: true }
})

const confirmed = ref(props.initialConfirmed)
const blockHash = ref(props.initialBlockHash)
const blockHeight = ref(props.initialBlockHeight)
const blockTime = ref(props.initialBlockTime)
const confirmations = ref(props.initialConfirmations)
const time = ref(props.initialBlockTime || null)
const currentTipHeight = ref(props.initialBlockTipHeight)

let pollingTimer = null

const formatNumber = (num) => {
  if (!num && num !== 0) return ''
  return new Intl.NumberFormat('en-US').format(num)
}

const formatAmount = (amount) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 8
  }).format(amount)
}

const splitAmount = (amount) => {
  const formatted = formatAmount(amount)
  const parts = formatted.split('.')
  return {
    whole: parts[0],
    decimal: parts[1] ? '.' + parts[1] : ''
  }
}

const initAlpine = async () => {
  await nextTick()
  try {
    if (window.Alpine) {
      window.Alpine.initTree(document.querySelector('[data-vue="transaction-details"]'))
    }
  } catch (_) {}
}

const updatePollingInterval = () => {
  if (pollingTimer) clearInterval(pollingTimer)
  
  if (document.visibilityState !== 'visible') {
    pollingTimer = null
    return
  }

  const interval = confirmed.value ? 60000 : 10000 // 60s if confirmed, 10s if not
  pollingTimer = setInterval(tick, interval)
}

const handleVisibilityChange = () => {
  if (document.visibilityState === 'visible') {
    tick() // Immediate tick when coming back
  }
  updatePollingInterval()
}

const tick = async () => {
  try {
    // 1. Fetch latest tip height
    const tipRes = await fetch(props.tipHeightApiUrl)
    const newTip = parseInt(await tipRes.text(), 10)
    if (!isNaN(newTip)) {
      currentTipHeight.value = newTip
    }

    // 2. Fetch transaction status
    const statusRes = await fetch(props.statusApiUrl)
    const data = await statusRes.json()

    const wasConfirmed = confirmed.value
    
    if (data.confirmed) {
      confirmed.value = true
      blockHash.value = data.block_hash
      blockHeight.value = data.block_height
      blockTime.value = data.block_time
      time.value = data.block_time
      confirmations.value = Math.max(1, currentTipHeight.value - data.block_height + 1)
      
      if (!wasConfirmed) {
        await initAlpine()
        updatePollingInterval() // Slow down polling
      }
    }
  } catch (e) {
    console.error('Polling failed', e)
  }
}

onMounted(() => {
  document.addEventListener('visibilitychange', handleVisibilityChange)
  updatePollingInterval()
  initAlpine()
})

onUnmounted(() => {
  document.removeEventListener('visibilitychange', handleVisibilityChange)
  if (pollingTimer) {
    clearInterval(pollingTimer)
  }
})
</script>
