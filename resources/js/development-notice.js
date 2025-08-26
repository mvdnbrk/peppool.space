/**
 * Development Notice Widget
 * Handles minimize/expand functionality and state persistence
 */
document.addEventListener('DOMContentLoaded', function() {
    const notice = document.getElementById('dev-notice');
    const minimizedPepe = document.getElementById('pepe-minimized');
    const minimizeBtn = document.getElementById('minimize-dev-notice');
    const pepeAvatar = document.getElementById('pepe-avatar');
    const storageKey = 'peppool-dev-notice-state';

    // Early exit if elements don't exist on this page
    if (!notice || !minimizedPepe || !minimizeBtn || !pepeAvatar) {
        return;
    }

    // Check previous state: 'dismissed', 'minimized', or null (show full)
    const noticeState = localStorage.getItem(storageKey);

    if (noticeState === 'minimized') {
        minimizedPepe.style.display = 'block';
    } else if (noticeState !== 'dismissed') {
        notice.style.display = 'block';
    }

    // Handle minimize button click
    minimizeBtn.addEventListener('click', function() {
        notice.style.display = 'none';
        minimizedPepe.style.display = 'block';
        localStorage.setItem(storageKey, 'minimized');
    });

    // Handle minimized Pepe click to expand
    minimizedPepe.addEventListener('click', function() {
        minimizedPepe.style.display = 'none';
        notice.style.display = 'block';
        localStorage.removeItem(storageKey);
    });

    // Handle Pepe avatar click in expanded notice (same as minimize)
    pepeAvatar.addEventListener('click', function() {
        notice.style.display = 'none';
        minimizedPepe.style.display = 'block';
        localStorage.setItem(storageKey, 'minimized');
    });
});
