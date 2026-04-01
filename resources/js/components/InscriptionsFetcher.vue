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
    :title="showTitle ? `${total.toLocaleString('en-US')} ${total === 1 ? 'Inscription' : 'Inscriptions'}` : null"
    :initially-expanded="expanded"
    @reach-end="loadMore"
  />

  <div v-if="loadingMore" class="mt-4 p-4 flex justify-center">
    <div class="h-6 w-6 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
  </div>

  <div v-else class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700 p-6 text-center">
    <p class="text-sm text-gray-500 dark:text-gray-400">No inscriptions found.</p>
  </div>
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
    },
    showTitle: {
      type: Boolean,
      default: true
    },
    expanded: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      inscriptions: [],
      total: 0,
      page: 1,
      lastPage: 1,
      loading: true,
      loadingMore: false,
      error: false,
      pollingInterval: null
    }
  },
  mounted() {
    this.fetchInscriptions(1)
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
    async fetchInscriptions(page = 1) {
      if (page === 1) this.loading = true
      else this.loadingMore = true

      try {
        const [path, query] = this.url.split('?')
        const searchParams = new URLSearchParams(query || '')
        searchParams.set('page', page.toString())
        const url = `${path}?${searchParams.toString()}`

        const response = await fetch(url)
        if (!response.ok) throw new Error('Network response was not ok')
        const data = await response.json()

        if (page === 1) {
          this.inscriptions = data.inscriptions || []
        } else {
          this.inscriptions = [...this.inscriptions, ...(data.inscriptions || [])]
        }

        this.total = data.total ?? this.inscriptions.length
        this.page = data.current_page || page
        this.lastPage = data.last_page || 1
        this.error = false
      } catch {
        this.error = true
      } finally {
        this.loading = false
        this.loadingMore = false
      }
    },
    loadMore() {
      if (this.loadingMore || this.page >= this.lastPage) return
      this.fetchInscriptions(this.page + 1)
    },
    startPolling() {
      if (this.pollingInterval) return
      this.pollingInterval = setInterval(() => {
        this.fetchInscriptions(1)
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
