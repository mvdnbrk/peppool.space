<!-- Floating Theme Toggle Component with Alpine.js Tooltips -->
<div 
    x-data="{
        showTooltip: false,
        tooltipText: '',
        hoveredButton: null,
        setTooltip(text, buttonElement) {
            this.tooltipText = text;
            this.hoveredButton = buttonElement;
            this.showTooltip = true;
        },
        hideTooltip() {
            this.showTooltip = false;
            this.hoveredButton = null;
        }
    }"
    class="fixed bottom-4 right-4 z-50 hidden md:block" 
    id="theme-toggle-widget"
>
    <!-- Sleek Tooltip with Arrow -->
    <div 
        x-show="showTooltip && hoveredButton"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-x-2"
        x-transition:enter-end="opacity-100 scale-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-x-0"
        x-transition:leave-end="opacity-0 scale-95 translate-x-2"
        class="fixed z-60 pointer-events-none"
        :style="hoveredButton ? `right: ${window.innerWidth - hoveredButton.getBoundingClientRect().left + 20}px; top: ${hoveredButton.getBoundingClientRect().top + window.scrollY + hoveredButton.offsetHeight / 2}px; transform: translateY(-50%);` : ''"
        x-cloak
    >
        <!-- Tooltip Content -->
        <div class="relative flex items-center">
            <div class="bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium px-3 py-2 rounded-lg shadow-xl backdrop-blur-sm">
                <span x-text="tooltipText" class="whitespace-nowrap"></span>
            </div>
            <!-- Arrow pointing to button -->
            <div class="absolute left-full top-1/2 transform -translate-y-1/2">
                <div class="w-0 h-0 border-l-[6px] border-t-[6px] border-b-[6px] border-l-gray-900 dark:border-l-gray-700 border-t-transparent border-b-transparent"></div>
            </div>
        </div>
    </div>

    <div class="flex flex-col items-center bg-white/90 dark:bg-gray-800/90 rounded-xl p-1.5 border border-gray-200 dark:border-gray-600 shadow-lg backdrop-blur-sm">
        <!-- Light Mode -->
        <button 
            type="button" 
            class="theme-toggle-btn flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 mb-0.5 cursor-pointer text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white"
            @click="setTheme('light')"
            @mouseenter="setTooltip('Light mode', $el)"
            @mouseleave="hideTooltip"
            @focus="setTooltip('Light mode', $el)"
            @blur="hideTooltip"
            data-theme="light"
            aria-label="Switch to light theme"
        >
            <x-icon-sun class="w-4 h-4" />
        </button>

        <!-- System Mode -->
        <button 
            type="button" 
            class="theme-toggle-btn flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 mb-0.5 cursor-pointer text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white"
            @click="setTheme('system')"
            @mouseenter="setTooltip('System preference', $el)"
            @mouseleave="hideTooltip"
            @focus="setTooltip('System preference', $el)"
            @blur="hideTooltip"
            data-theme="system"
            aria-label="Switch to system theme preference"
        >
            <x-icon-computer-desktop class="w-4 h-4" />
        </button>

        <!-- Dark Mode -->
        <button 
            type="button" 
            class="theme-toggle-btn flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 cursor-pointer text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white"
            @click="setTheme('dark')"
            @mouseenter="setTooltip('Dark mode', $el)"
            @mouseleave="hideTooltip"
            @focus="setTooltip('Dark mode', $el)"
            @blur="hideTooltip"
            data-theme="dark"
            aria-label="Switch to dark theme"
        >
            <x-icon-moon class="w-4 h-4" />
        </button>
    </div>
</div>

<style>
.theme-toggle-btn.active {
    background-color: rgb(220 252 231) !important;
    color: rgb(21 128 61) !important;
}

.dark .theme-toggle-btn.active {
    background-color: rgb(22 101 52) !important;
    color: rgb(134 239 172) !important;
}

.theme-toggle-btn.active svg {
    transform: scale(1.1);
}

.theme-toggle-btn:hover {
    background-color: rgb(243 244 246) !important;
}

.dark .theme-toggle-btn:hover {
    background-color: rgb(55 65 81) !important;
}
</style>

<script>
window.setTheme = function(theme) {
    const html = document.documentElement;
    
    // Apply theme
    if (theme === 'dark') {
        html.classList.add('dark');
    } else if (theme === 'light') {
        html.classList.remove('dark');
    } else {
        // System preference
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }
    }
    
    // Update active button
    const buttons = document.querySelectorAll('.theme-toggle-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    const activeButton = document.querySelector(`[data-theme="${theme}"]`);
    if (activeButton) {
        activeButton.classList.add('active');
    }
    
    // Save to localStorage
    localStorage.setItem('theme', theme);
};

// Initialize theme on page load
function initTheme() {
    const savedTheme = localStorage.getItem('theme') || 'system';
    window.setTheme(savedTheme);
}

// Initialize when ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTheme);
} else {
    initTheme();
}

// Handle system theme changes
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
    const currentTheme = localStorage.getItem('theme') || 'system';
    if (currentTheme === 'system') {
        window.setTheme('system');
    }
});
</script>

