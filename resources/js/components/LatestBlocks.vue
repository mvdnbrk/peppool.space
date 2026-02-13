<template>
  <div ref="root">
    <TransitionGroup name="blocks" tag="div" class="blocks-list">
      <a
        v-for="block in blocks"
        :key="Number(block.height)"
        :href="blockRoute.replace('__HEIGHT__', block.height)"
        class="block px-6 py-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
              <div class="px-3 py-1 bg-green-100 dark:bg-green-900 rounded-md flex items-center justify-center">
                <span class="text-sm font-semibold text-green-600 dark:text-green-400">{{ block.height }}</span>
              </div>
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-sm font-medium text-gray-900 dark:text-white">
                {{ (block.id || block.hash || '').substring(0, 16) }}...{{ (block.id || block.hash || '').substring((block.id || block.hash || '').length - 8) }}
              </p>
              <timestamp
                x-data="timestamp"
                :datetime="toIso(block.timestamp ?? block.time)"
                x-text="relativeTime"
                class="text-sm text-gray-500 dark:text-gray-400"
              ></timestamp>
            </div>
          </div>
          <div class="text-right">
            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ block.tx_count }} txs</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ formatSize(block.size) }}</p>
          </div>
        </div>
      </a>
    </TransitionGroup>

    <div v-if="blocks.length === 0" class="px-6 py-8 text-center">
      <p class="text-gray-500 dark:text-gray-400">No blocks available</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue'

const props = defineProps({
  initialBlocks: { type: Array, default: () => [] },
  apiUrl: { type: String, required: true },
  intervalMs: { type: Number, default: 10000 },
  blockRoute: { type: String, default: '' }
})

const blocks = ref([...(props.initialBlocks || [])])
const root = ref(null)
let timer = null
let inFlight = false
let controller = null

const updateHeightCard = () => {
  const el = document.getElementById('current-block-height')
  if (!el) return
  const maxH = blocks.value.reduce((m, b) => Math.max(m, Number(b.height) || 0), 0)
  if (maxH > 0) el.innerText = maxH.toLocaleString('en-US')
}

const formatSize = (bytes) => {
  if (!bytes) return '0 KB'
  return `${(bytes / 1024).toFixed(1)} KB`
}

const toIso = (unixTs) => {
  const ts = Number(unixTs)
  if (!ts || Number.isNaN(ts)) return ''
  return new Date(ts * 1000).toISOString()
}

const fetchBlocks = async () => {
  if (inFlight) return
  inFlight = true

  if (controller) {
    try { controller.abort() } catch (_) {}
  }
  controller = new AbortController()

  try {
    const res = await fetch(props.apiUrl, { signal: controller.signal })
    if (!res.ok) return
    const data = await res.json()
    if (!Array.isArray(data)) return

    // Build maps for quick lookup
    const existingByHeight = new Map(blocks.value.map(b => [Number(b.height), b]))

    // Merge new blocks: ensure order by height desc, dedupe by height
    const merged = [...data, ...blocks.value]
      .filter(Boolean)
      .sort((a, b) => Number(b.height) - Number(a.height))

    const seen = new Set()
    const deduped = []
    for (const b of merged) {
      const h = Number(b.height)
      if (seen.has(h)) continue
      seen.add(h)
      deduped.push(b)
    }

    blocks.value = deduped.slice(0, 10)
    updateHeightCard()
    await initAlpine()
  } catch (err) {
    if (err?.name !== 'AbortError') {
      console.error('Error fetching latest blocks:', err)
    }
  } finally {
    inFlight = false
    controller = null
  }
}

const startPolling = () => {
  fetchBlocks()
  if (!timer) timer = setInterval(fetchBlocks, props.intervalMs)
}

const stopPolling = () => {
  if (timer) { clearInterval(timer); timer = null }
}

onMounted(async () => {
  // Normalize initial list to match fetch ordering (height desc) to avoid initial flip
  if (Array.isArray(blocks.value) && blocks.value.length > 0) {
    const seen = new Set()
    const sorted = [...blocks.value]
      .filter(Boolean)
      .sort((a, b) => Number(b.height) - Number(a.height))
      .filter(b => (seen.has(Number(b.height)) ? false : (seen.add(Number(b.height)), true)))
      .slice(0, 10)
    blocks.value = sorted
  }
  updateHeightCard()
  await initAlpine()
  startPolling()
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') startPolling()
    else stopPolling()
  })
})

onUnmounted(() => {
  stopPolling()
  if (controller) { try { controller.abort() } catch (_) {} }
})

// Initialize Alpine on dynamically rendered content
const initAlpine = async () => {
  await nextTick()
  try {
    if (window.Alpine && root.value) {
      window.Alpine.initTree(root.value)
    }
  } catch (_) {}
}

// Also re-init when blocks array changes length/content
watch(blocks, async () => {
  await initAlpine()
})
</script>

<style scoped>
.blocks-enter-active { transition: all 0.4s ease; }
.blocks-leave-active { transition: all 0.8s ease; }
.blocks-enter-from { opacity: 0; transform: translateY(16px); }
.blocks-leave-to { opacity: 0; transform: translateY(-8px); }
.blocks-move { transition: transform 0.4s ease; }
</style>
