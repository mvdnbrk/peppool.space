function getNextUpdateDelay(diffInSeconds) {
    if (diffInSeconds < 60) {
        // Seconds change every 1s
        return 1000;
    } else if (diffInSeconds < 3600) {
        // Minutes change every 60s; delay to next minute boundary
        return (60 - (diffInSeconds % 60)) * 1000;
    } else if (diffInSeconds < 86400) {
        // Hours change every 3600s; delay to next hour boundary
        return (3600 - (diffInSeconds % 3600)) * 1000;
    } else {
        // Days change every 86400s; delay to next day boundary
        return (86400 - (diffInSeconds % 86400)) * 1000;
    }
}

function updateTimestamp(element) {
    const timestampStr = element.getAttribute('datetime');
    if (!timestampStr) return; // Skip if no datetime

    const pastDate = new Date(timestampStr);
    if (isNaN(pastDate.getTime())) return; // Skip invalid

    const now = new Date();
    let diffInSeconds = Math.floor((now - pastDate) / 1000);
    const isFuture = diffInSeconds < 0;
    diffInSeconds = Math.abs(diffInSeconds);

    let relativeTime;
    if (diffInSeconds < 60) {
        relativeTime = `${diffInSeconds} second${diffInSeconds !== 1 ? 's' : ''}`;
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        relativeTime = `${minutes} minute${minutes !== 1 ? 's' : ''}`;
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        relativeTime = `${hours} hour${hours !== 1 ? 's' : ''}`;
    } else {
        const days = Math.floor(diffInSeconds / 86400);
        relativeTime = `${days} day${days !== 1 ? 's' : ''}`;
    }

    relativeTime = isFuture ? `in ${relativeTime}` : `${relativeTime} ago`;
    element.innerText = relativeTime;

    // Schedule next update for this element only
    const nextDelay = getNextUpdateDelay(diffInSeconds);
    setTimeout(() => updateTimestamp(element), nextDelay);
}

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    const timestamps = document.querySelectorAll('timestamp');
    timestamps.forEach(element => {
        updateTimestamp(element); // Initial update and start scheduling
    });
});
