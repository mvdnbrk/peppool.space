import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import { createApp } from 'vue';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.plugin(focus);
Alpine.start();

// Initialize Vue.js
import MempoolTransactions from './components/MempoolTransactions.vue';
import LatestBlocks from './components/LatestBlocks.vue';
import PriceChart from './components/PriceChart.vue';

// Auto-mount Vue components
document.addEventListener('DOMContentLoaded', () => {
    // Mount individual Vue components
    const components = {
        'mempool-transactions': MempoolTransactions,
        'latest-blocks': LatestBlocks,
        'price-chart': PriceChart,
    };

    Object.entries(components).forEach(([name, component]) => {
        const elements = document.querySelectorAll(`[data-vue="${name}"]`);
        elements.forEach(element => {
            const props = element.dataset.props ? JSON.parse(element.dataset.props) : {};
            createApp(component, props).mount(element);
        });
    });
});

import './bootstrap';
import './timestamps';
import './polling';
