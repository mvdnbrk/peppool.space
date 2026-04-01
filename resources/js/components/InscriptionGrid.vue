<template>
  <div class="bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700">
    <div v-if="title" class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
      <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400">
        {{ title }}
      </h2>
    </div>
    <div class="p-4">
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
        <InscriptionThumbnail
          v-for="id in displayedInscriptions"
          :key="id"
          :inscription-id="id"
        />
      </div>
      <div v-if="!expanded && total > limit" class="mt-4 text-center">
        <button
          @click="expand"
          class="text-sm text-blue-600 dark:text-blue-400 hover:underline cursor-pointer"
        >
          Show all {{ total.toLocaleString('en-US') }} inscriptions
        </button>
      </div>
      <div v-if="expanded && visibleCount < inscriptions.length" ref="sentinel" class="h-1"></div>
    </div>
  </div>
</template>

<script>
import InscriptionThumbnail from './InscriptionThumbnail.vue'

const BATCH_SIZE = 18

export default {
  name: 'InscriptionGrid',
  components: { InscriptionThumbnail },
  props: {
    inscriptions: {
      type: Array,
      required: true
    },
    total: {
      type: Number,
      required: true
    },
    limit: {
      type: Number,
      default: 6
    },
    title: {
      type: String,
      default: null
    },
    initiallyExpanded: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      expanded: this.initiallyExpanded,
      visibleCount: this.initiallyExpanded ? BATCH_SIZE : 0,
      observer: null
    }
  },
  mounted() {
    if (this.expanded) {
      this.$nextTick(() => this.setupObserver())
    }
  },
  computed: {
    displayedInscriptions() {
      if (!this.expanded) {
        return this.inscriptions.slice(0, this.limit)
      }
      return this.inscriptions.slice(0, this.visibleCount)
    }
  },
  beforeUnmount() {
    this.destroyObserver()
  },
  methods: {
    expand() {
      this.expanded = true
      this.visibleCount = this.limit + BATCH_SIZE
      this.$nextTick(() => this.setupObserver())
    },
    setupObserver() {
      this.destroyObserver()
      const sentinel = this.$refs.sentinel
      if (!sentinel) return

      this.observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting && this.visibleCount < this.inscriptions.length) {
          this.visibleCount = Math.min(this.visibleCount + BATCH_SIZE, this.inscriptions.length)
          this.$nextTick(() => {
            if (this.visibleCount >= this.inscriptions.length) {
              this.destroyObserver()
            }
          })
        }
      }, { rootMargin: '200px' })

      this.observer.observe(sentinel)
    },
    destroyObserver() {
      if (this.observer) {
        this.observer.disconnect()
        this.observer = null
      }
    }
  }
}
</script>
