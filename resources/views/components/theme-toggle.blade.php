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
            <x-icon-sun class="w-4 h-4" />
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
            <x-icon-computer-desktop class="w-4 h-4" />
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

