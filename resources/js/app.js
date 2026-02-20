import '@tailwindplus/elements';
import 'fslightbox';

// Auto-mount Vue components dynamically
document.addEventListener('DOMContentLoaded', () => {
    const vueElements = document.querySelectorAll('[data-vue]');
    const timestampElements = document.querySelectorAll('timestamp');
    
    if (vueElements.length > 0 || timestampElements.length > 0) {
        import('vue').then(({ createApp }) => {
            const components = {
                'mempool-transactions': () => import('./components/MempoolTransactions.vue'),
                'latest-blocks': () => import('./components/LatestBlocks.vue'),
                'price-chart': () => import('./components/PriceChart.vue'),
                'address-transactions': () => import('./components/AddressTransactions.vue'),
                'currency-converter': () => import('./components/CurrencyConverter.vue'),
                'transaction-details': () => import('./components/TransactionDetails.vue'),
                'timestamp': () => import('./components/Timestamp.vue'),
                'theme-toggle': () => import('./components/ThemeToggle.vue'),
                'pepe-price': () => import('./components/PepePrice.vue'),
                'block-height': () => import('./components/BlockHeight.vue'),
                'mempool-count': () => import('./components/MempoolCount.vue'),
                'waitlist-form': () => import('./components/WaitlistForm.vue'),
            };

            // Mount standard [data-vue] components
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

            // Specifically handle <timestamp> tags
            if (timestampElements.length > 0) {
                components['timestamp']().then(module => {
                    timestampElements.forEach(element => {
                        // Skip if already handled by data-vue logic (unlikely but safe)
                        if (element.dataset.vApp !== undefined) return;
                        
                        const props = {
                            datetime: element.getAttribute('datetime')
                        };
                        createApp(module.default, props).mount(element);
                    });
                });
            }
        });
    }
});

import './bootstrap';
