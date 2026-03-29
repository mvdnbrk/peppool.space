<template>
  <div v-if="loading" class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
      <div class="h-4 w-32 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
    </div>
    <div class="p-4">
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
        <div v-for="n in 6" :key="n" class="aspect-square rounded-lg bg-gray-100 dark:bg-gray-800 animate-pulse"></div>
      </div>
    </div>
  </div>

  <div v-else-if="error && !inscriptions.length" class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700 p-6 text-center">
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Could not load inscriptions.</p>
    <button @click="fetchInscriptions" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Retry</button>
  </div>

  <InscriptionGrid
    v-else-if="inscriptions.length > 0"
    :inscriptions="inscriptions"
    :total="total"
    :title="`${total.toLocaleString('en-US')} ${total === 1 ? 'Inscription' : 'Inscriptions'}`"
  />
</template>

<script>
import InscriptionGrid from './InscriptionGrid.vue'

export default {
  name: 'InscriptionsFetcher',
  components: { InscriptionGrid },
  props: {
    url: {
      type: String,
      required: true
    },
    poll: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      inscriptions: [],
      total: 0,
      loading: true,
      error: false,
      pollingInterval: null
    }
  },
  mounted() {
    this.fetchInscriptions()
    if (this.poll) {
      this.startPolling()
      document.addEventListener('visibilitychange', this.handleVisibilityChange)
    }
  },
  beforeUnmount() {
    this.stopPolling()
    if (this.poll) {
      document.removeEventListener('visibilitychange', this.handleVisibilityChange)
    }
  },
  methods: {
    async fetchInscriptions() {
      try {
        const response = await fetch(this.url)
        if (!response.ok) throw new Error('Network response was not ok')
        const data = await response.json()
        this.inscriptions = data.inscriptions || []
        this.total = data.total ?? this.inscriptions.length
        this.error = false
      } catch {
        this.error = true
      } finally {
        this.loading = false
      }
    },
    startPolling() {
      if (this.pollingInterval) return
      this.pollingInterval = setInterval(() => {
        this.fetchInscriptions()
      }, 60000)
    },
    stopPolling() {
      if (this.pollingInterval) {
        clearInterval(this.pollingInterval)
        this.pollingInterval = null
      }
    },
    handleVisibilityChange() {
      if (document.hidden) {
        this.stopPolling()
      } else {
        this.fetchInscriptions()
        this.startPolling()
      }
    }
  }
}
</script>
