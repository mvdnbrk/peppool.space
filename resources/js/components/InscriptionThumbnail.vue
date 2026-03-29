<template>
  <a :href="`/inscription/${inscriptionId}`" class="block aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800 transition-transform hover:scale-105">
    <img
      v-if="!failed"
      :src="`/content/${inscriptionId}`"
      :alt="`Inscription ${inscriptionId}`"
      class="w-full h-full object-cover [image-rendering:pixelated]"
      loading="lazy"
      @error="failed = true"
    />
    <div v-else class="w-full h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
      <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
      </svg>
      <span class="text-xs">View</span>
    </div>
  </a>
</template>

<script>
export default {
  name: 'InscriptionThumbnail',
  props: {
    inscriptionId: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      failed: false
    }
  }
}
</script>
