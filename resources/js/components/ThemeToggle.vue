<template>
  <div class="fixed bottom-4 right-4 z-50 hidden md:block">
    <!-- Sleek Tooltip with Arrow -->
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0 scale-95 translate-x-2"
      enter-to-class="opacity-100 scale-100 translate-x-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100 scale-100 translate-x-0"
      leave-to-class="opacity-0 scale-95 translate-x-2"
    >
      <div 
        v-if="showTooltip"
        class="fixed z-60 pointer-events-none"
        :style="tooltipStyle"
      >
        <!-- Tooltip Content -->
        <div class="relative flex items-center">
          <div class="bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium px-3 py-2 rounded-lg shadow-xl backdrop-blur-sm">
            <span class="whitespace-nowrap">{{ tooltipText }}</span>
          </div>
          <!-- Arrow pointing to button -->
          <div class="absolute left-full top-1/2 transform -translate-y-1/2">
            <div class="w-0 h-0 border-l-[6px] border-t-[6px] border-b-[6px] border-l-gray-900 dark:border-l-gray-700 border-t-transparent border-b-transparent"></div>
          </div>
        </div>
      </div>
    </Transition>

    <div class="flex flex-col items-center bg-white/90 dark:bg-gray-800/90 rounded-xl p-1.5 border border-gray-200 dark:border-gray-600 shadow-lg backdrop-blur-sm">
      <!-- Light Mode -->
      <button 
        type="button" 
        :class="['theme-toggle-btn flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 mb-0.5 cursor-pointer text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white', { active: currentTheme === 'light' }]"
        @click="setTheme('light')"
        @mouseenter="handleMouseEnter('Light mode', $event)"
        @mouseleave="hideTooltip"
        @focus="handleMouseEnter('Light mode', $event)"
        @blur="hideTooltip"
        aria-label="Switch to light theme"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.95 16.95l.707.707M7.05 7.05l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
        </svg>
      </button>

      <!-- System Mode -->
      <button 
        type="button" 
        :class="['theme-toggle-btn flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 mb-0.5 cursor-pointer text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white', { active: currentTheme === 'system' }]"
        @click="setTheme('system')"
        @mouseenter="handleMouseEnter('System preference', $event)"
        @mouseleave="hideTooltip"
        @focus="handleMouseEnter('System preference', $event)"
        @blur="hideTooltip"
        aria-label="Switch to system theme preference"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
      </button>

      <!-- Dark Mode -->
      <button 
        type="button" 
        :class="['theme-toggle-btn flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 cursor-pointer text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white', { active: currentTheme === 'dark' }]"
        @click="setTheme('dark')"
        @mouseenter="handleMouseEnter('Dark mode', $event)"
        @mouseleave="hideTooltip"
        @focus="handleMouseEnter('Dark mode', $event)"
        @blur="hideTooltip"
        aria-label="Switch to dark theme"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'

const currentTheme = ref('system')
const showTooltip = ref(false)
const tooltipText = ref('')
const tooltipPosition = ref({ x: 0, y: 0 })
const activeButton = ref(null)

const tooltipStyle = computed(() => ({
  left: `${tooltipPosition.value.x}px`,
  top: `${tooltipPosition.value.y}px`,
  transform: 'translate(-100%, -50%)'
}))

const setTheme = (theme) => {
  currentTheme.value = theme
  localStorage.setItem('theme', theme)
  applyTheme()
}

const applyTheme = () => {
  const theme = currentTheme.value
  const html = document.documentElement
  
  if (theme === 'dark') {
    html.classList.add('dark')
  } else if (theme === 'light') {
    html.classList.remove('dark')
  } else {
    // System preference
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
      html.classList.add('dark')
    } else {
      html.classList.remove('dark')
    }
  }
}

const handleMouseEnter = (text, event) => {
  tooltipText.value = text
  activeButton.value = event.currentTarget
  updateTooltipPosition()
  showTooltip.ref = true // wait, it's showTooltip.value
  showTooltip.value = true
}

const hideTooltip = () => {
  showTooltip.value = false
  activeButton.value = null
}

const updateTooltipPosition = () => {
  if (activeButton.value) {
    const rect = activeButton.value.getBoundingClientRect()
    tooltipPosition.value = {
      x: rect.left - 16,
      y: rect.top + rect.height / 2
    }
  }
}

const handleScroll = () => {
  if (showTooltip.value) {
    updateTooltipPosition()
  }
}

onMounted(() => {
  currentTheme.value = localStorage.getItem('theme') || 'system'
  applyTheme()
  window.addEventListener('scroll', handleScroll, { passive: true })
  
  // Handle system theme changes
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    if (currentTheme.value === 'system') {
      applyTheme()
    }
  })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
.theme-toggle-btn.active {
    background-color: rgb(220 252 231) !important;
    color: rgb(21 128 61) !important;
}

:deep(.dark) .theme-toggle-btn.active {
    background-color: rgb(22 101 52) !important;
    color: rgb(134 239 172) !important;
}

.theme-toggle-btn.active svg {
    transform: scale(1.1);
}

.theme-toggle-btn:hover {
    background-color: rgb(243 244 246) !important;
}

:deep(.dark) .theme-toggle-btn:hover {
    background-color: rgb(55 65 81) !important;
}
</style>
