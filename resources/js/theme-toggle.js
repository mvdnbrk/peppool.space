/**
 * Theme Toggle Functionality
 * Handles dark/light/system theme switching with localStorage persistence
 * Now works with Alpine.js
 */

// Make setTheme available globally for Alpine.js
window.setTheme = function(theme) {
    const html = document.documentElement;
    
    // Save preference to localStorage
    localStorage.setItem('theme', theme);
    
    // Apply the theme
    if (theme === 'dark') {
        html.classList.add('dark');
    } else if (theme === 'light') {
        html.classList.remove('dark');
    } else { // system
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }
    }
    
    // Update active button
    document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-theme') === theme) {
            btn.classList.add('active');
        }
    });
};

// Initialize theme on page load
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme') || 'system';
    window.setTheme(savedTheme);
});

// Watch for system theme changes
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (localStorage.getItem('theme') === 'system') {
        window.setTheme('system');
    }
});
