import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.plugin(focus);
Alpine.start();

import './bootstrap';
import './timestamps';
import './polling';
