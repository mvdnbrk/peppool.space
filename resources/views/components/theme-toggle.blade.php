<!-- Floating Theme Toggle Component -->
<div class="fixed bottom-4 right-4 z-50 hidden md:block" id="theme-toggle-widget">
    <div class="flex flex-col items-center bg-white/90 dark:bg-gray-800/90 rounded-xl p-1.5 border border-gray-200 dark:border-gray-600 shadow-lg backdrop-blur-sm">
        <!-- Light Mode -->
        <button 
            type="button" 
            class="theme-toggle-btn flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 mb-0.5 cursor-pointer text-gray-600 dark:text-gray-400"
            onclick="setTheme('light')"
            data-theme="light"
            title="Light mode"
            aria-label="Switch to light theme"
        >
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <!-- Sun rays -->
                <g stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity="0.6">
                    <path d="M10 3V1M10 19v-2M17 10h2M1 10h2M15.66 4.34l1.41-1.41M2.93 17.07l1.41-1.41M15.66 15.66l1.41 1.41M2.93 2.93l1.41 1.41"/>
                </g>
                <!-- Sun center -->
                <circle cx="10" cy="10" r="3.5" fill="none" stroke="currentColor" stroke-width="1.5"/>
            </svg>
        </button>

        <!-- System Mode -->
        <button 
            type="button" 
            class="theme-toggle-btn flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 mb-0.5 cursor-pointer text-gray-600 dark:text-gray-400"
            onclick="setTheme('system')"
            data-theme="system"
            title="System preference"
            aria-label="Switch to system theme preference"
        >
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <!-- Monitor base -->
                <rect x="2" y="4" width="16" height="10" rx="1.5" fill="none" stroke="currentColor" stroke-width="1.5"/>
                <!-- Screen -->
                <rect x="3.5" y="5.5" width="13" height="7" rx="0.5" fill="none" stroke="currentColor" stroke-width="1" opacity="0.6"/>
                <!-- Stand -->
                <path d="M8 14v2h4v-2M6 16h8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </button>

        <!-- Dark Mode -->
        <button 
            type="button" 
            class="theme-toggle-btn flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200 cursor-pointer text-gray-600 dark:text-gray-400"
            onclick="setTheme('dark')"
            data-theme="dark"
            title="Dark mode"
            aria-label="Switch to dark theme"
        >
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <!-- Moon crescent outline -->
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" fill="none" stroke="currentColor" stroke-width="1.5"/>
                <!-- Moon inner detail -->
                <path d="M15.5 11.5A6.5 6.5 0 018.5 4.5a6.48 6.48 0 00-1.793 4.5A6.5 6.5 0 0015.5 11.5z" fill="none" stroke="currentColor" stroke-width="1" opacity="0.6"/>
            </svg>
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

