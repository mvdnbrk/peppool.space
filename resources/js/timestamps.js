import { formatDistanceToNow, parseISO, isToday, isYesterday, format } from 'date-fns';

const getNextUpdateDelay = (diffInSeconds) => {
    diffInSeconds = Math.abs(diffInSeconds);
    if (diffInSeconds < 60) return 1000; // 1 second
    if (diffInSeconds < 3600) return 60 * 1000; // 1 minute
    if (diffInSeconds < 86400) return 3600 * 1000; // 1 hour
    return -1; // No further updates needed for > 1 day
};

document.addEventListener('alpine:init', () => {
    Alpine.data('timestamp', () => ({
        relativeTime: 'Loading...',
        timeout: null,

        init() {
            this.update();
        },

        destroy() {
            if (this.timeout) {
                clearTimeout(this.timeout);
            }
        },

        update() {
            const isoString = this.$el.getAttribute('datetime');
            if (!isoString) {
                this.relativeTime = 'No date';
                return;
            }

            try {
                const date = parseISO(isoString);
                const diffInSeconds = (Date.now() - date.getTime()) / 1000;

                if (diffInSeconds < 60) {
                    const seconds = Math.max(0, Math.floor(diffInSeconds));
                    this.relativeTime = `${seconds} second${seconds === 1 ? '' : 's'} ago`;
                } else if (diffInSeconds < 3600) {
                    const minutes = Math.floor(diffInSeconds / 60);
                    this.relativeTime = `${minutes} minute${minutes === 1 ? '' : 's'} ago`;
                } else {
                    if (isToday(date)) {
                        this.relativeTime = `Today at ${format(date, 'HH:mm')}`;
                    } else if (isYesterday(date)) {
                        this.relativeTime = `Yesterday at ${format(date, 'HH:mm')}`;
                    } else {
                        this.relativeTime = format(date, 'yyyy-MM-dd HH:mm');
                    }
                }

                const nextDelay = getNextUpdateDelay(diffInSeconds);

                if (this.timeout) clearTimeout(this.timeout);
                if (nextDelay > 0) {
                    this.timeout = setTimeout(() => this.update(), nextDelay);
                }
            } catch (e) {
                console.error('Error parsing timestamp:', e);
                this.relativeTime = 'Invalid date';
                if (this.timeout) clearTimeout(this.timeout);
            }
        }
    }));
});
