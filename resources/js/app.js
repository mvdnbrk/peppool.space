import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import { createApp } from 'vue';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.plugin(focus);
Alpine.start();

// Auto-mount Vue components
document.addEventListener('DOMContentLoaded', () => {
    const components = {
        'mempool-transactions': () => import('./components/MempoolTransactions.vue'),
        'latest-blocks': () => import('./components/LatestBlocks.vue'),
        'price-chart': () => import('./components/PriceChart.vue'),
        'address-transactions': () => import('./components/AddressTransactions.vue'),
        'currency-converter': () => import('./components/CurrencyConverter.vue'),
    };

    Object.entries(components).forEach(([name, loadComponent]) => {
        const elements = document.querySelectorAll(`[data-vue="${name}"]`);
        
        if (elements.length > 0) {
            loadComponent().then(module => {
                elements.forEach(element => {
                    const props = element.dataset.props ? JSON.parse(element.dataset.props) : {};
                    createApp(module.default, props).mount(element);
                });
            });
        }
    });
});

import './bootstrap';
import './timestamps';
import './polling';
