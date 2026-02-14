<template>
  <span :title="titleText">{{ relativeTime }}</span>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue'
import { formatDistanceToNow, parseISO, isToday, isYesterday, format } from 'date-fns'

const props = defineProps({
  datetime: {
    type: String,
    required: true
  }
})

const relativeTime = ref('Loading...')
const timeout = ref(null)

const titleText = computed(() => {
  try {
    const date = parseISO(props.datetime)
    return date.toLocaleString()
  } catch (e) {
    return ''
  }
})

const getNextUpdateDelay = (diffInSeconds) => {
  diffInSeconds = Math.abs(diffInSeconds)
  if (diffInSeconds < 60) return 1000 // 1 second
  if (diffInSeconds < 3600) return 60 * 1000 // 1 minute
  if (diffInSeconds < 86400) return 3600 * 1000 // 1 hour
  return -1 // No further updates needed for > 1 day
}

const update = () => {
  if (!props.datetime) {
    relativeTime.value = 'No date'
    return
  }

  try {
    const date = parseISO(props.datetime)
    const diffInSeconds = (Date.now() - date.getTime()) / 1000

    if (diffInSeconds < 60) {
      const seconds = Math.max(0, Math.floor(diffInSeconds))
      relativeTime.value = `${seconds} second${seconds === 1 ? '' : 's'} ago`
    } else if (diffInSeconds < 3600) {
      const minutes = Math.floor(diffInSeconds / 60)
      relativeTime.value = `${minutes} minute${minutes === 1 ? '' : 's'} ago`
    } else {
      if (isToday(date)) {
        relativeTime.value = `Today at ${format(date, 'HH:mm')}`
      } else if (isYesterday(date)) {
        relativeTime.value = `Yesterday at ${format(date, 'HH:mm')}`
      } else {
        relativeTime.value = format(date, 'yyyy-MM-dd HH:mm')
      }
    }

    const nextDelay = getNextUpdateDelay(diffInSeconds)

    if (timeout.value) clearTimeout(timeout.value)
    if (nextDelay > 0) {
      timeout.value = setTimeout(update, nextDelay)
    }
  } catch (e) {
    console.error('Error parsing timestamp:', e)
    relativeTime.value = 'Invalid date'
    if (timeout.value) clearTimeout(timeout.value)
  }
}

watch(() => props.datetime, () => {
  update()
})

onMounted(() => {
  update()
})

onUnmounted(() => {
  if (timeout.value) {
    clearTimeout(timeout.value)
  }
})
</script>
