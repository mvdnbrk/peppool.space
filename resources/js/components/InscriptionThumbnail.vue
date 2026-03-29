<template>
  <a :href="`/inscription/${inscriptionId}`" class="block aspect-square rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800 transition-transform hover:scale-105">
    <img
      v-if="mode === 'img'"
      :src="contentUrl"
      :alt="`Inscription ${inscriptionId}`"
      class="w-full h-full object-cover [image-rendering:pixelated]"
      loading="lazy"
      @error="mode = 'iframe'"
    />
    <iframe
      v-else-if="mode === 'iframe'"
      :src="contentUrl"
      class="w-full h-full border-0 pointer-events-none"
      sandbox=""
      loading="lazy"
      scrolling="no"
    ></iframe>
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
      mode: 'img'
    }
  },
  computed: {
    contentUrl() {
      return `/content/${this.inscriptionId}`
    }
  }
}
</script>
