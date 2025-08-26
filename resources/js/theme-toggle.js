/**
 * Theme Toggle Functionality
 * Handles dark/light/system theme switching with localStorage persistence
 */

// Simple function-based approach
function initThemeToggle() {
    const toggleButtons = document.querySelectorAll('.theme-toggle-btn');
    const html = document.documentElement;
    
    console.log('Theme toggle init - buttons found:', toggleButtons.length);
    
    if (toggleButtons.length === 0) {
        console.warn('No theme toggle buttons found');
        return;
    }
    
    // Get saved theme or default to system
    const savedTheme = localStorage.getItem('theme') || 'system';
    console.log('Saved theme:', savedTheme);
    
    // Apply theme functions
    function applyTheme(theme) {
        console.log('Applying theme:', theme);
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
    }
    
    function updateActiveButton(theme) {
        toggleButtons.forEach(btn => btn.classList.remove('active'));
        const activeButton = document.querySelector(`[data-theme="${theme}"]`);
        if (activeButton) {
            activeButton.classList.add('active');
        }
    }
    
    // Apply theme on load
    applyTheme(savedTheme);
    updateActiveButton(savedTheme);
    
    // Add click listeners
    toggleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const theme = this.dataset.theme;
            console.log('Theme button clicked:', theme);
            applyTheme(theme);
            updateActiveButton(theme);
            localStorage.setItem('theme', theme);
        });
    });
    
    // Listen for system theme changes when in system mode
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
        const currentTheme = localStorage.getItem('theme') || 'system';
        if (currentTheme === 'system') {
            applyTheme('system');
        }
    });
}

// Initialize multiple ways to ensure it works
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initThemeToggle);
} else {
    initThemeToggle();
}

// Also try after a short delay
setTimeout(initThemeToggle, 500);
