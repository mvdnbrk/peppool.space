<template>
  <div class="bg-white dark:bg-gray-900 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
    <!-- Header -->
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
      <div class="flex flex-col gap-3">
        <div class="flex flex-col gap-3 sm:grid sm:grid-cols-[1fr_auto] sm:items-center">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white sm:min-w-0 truncate">Transactions</h2>
          <div class="flex items-center gap-2 sm:justify-end">
            <span class="text-sm text-gray-500 dark:text-gray-400">Per page:</span>
            <div class="inline-flex gap-1 rounded-full bg-gray-950/5 p-1 dark:bg-white/10">
              <a
                v-for="perPage in perPageOptions"
                :key="perPage"
                :href="buildUrlForPerPage(perPage)"
                @click="rememberPerPage(perPage)"
                class="px-3 py-1 text-sm rounded-full font-medium cursor-pointer"
                :class="perPage === currentPerPage
                  ? 'bg-white ring ring-gray-950/5 dark:bg-gray-600 dark:ring-0 text-gray-900 dark:text-white'
                  : 'text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white'"
              >
                {{ perPage }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Transaction List -->
    <div class="divide-y divide-gray-200 dark:divide-gray-700">
      <div
        v-for="tx in transactions"
        :key="tx.txid"
        class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800"
      >
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
          <div class="flex-1 min-w-0">
            <a :href="txRoute.replace('__TXID__', tx.txid)" class="font-mono text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 break-all">
              {{ tx.txid }}
            </a>
            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              <span v-if="tx.confirmations === 0" class="text-red-500 font-medium">Unconfirmed</span>
              <Timestamp
                v-else-if="tx.time || tx.timereceived"
                :datetime="new Date((tx.timereceived || tx.time) * 1000).toISOString()"
              />
              <span v-if="tx.is_coinbase" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 ml-2">
                Coinbase
              </span>
            </div>
          </div>
          <div class="mt-2 sm:mt-0 text-sm font-medium text-right">
            <div v-if="tx.is_incoming" class="flex items-center justify-end text-green-600">
              <span class="text-green-600">
                <span>{{ splitAmount(tx.amount).whole }}</span><span class="text-[0.85em] text-gray-500 dark:text-gray-400 font-normal">{{ splitAmount(tx.amount).decimal }}</span>
                PEPE
              </span>
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" role="img" aria-label="Received" focusable="false">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
              </svg>
              <span class="sr-only">Received</span>
            </div>
            <div v-else class="flex items-center justify-end text-red-600">
              <span class="text-red-600">
                <span>{{ splitAmount(tx.amount).whole }}</span><span class="text-[0.85em] text-gray-500 dark:text-gray-400 font-normal">{{ splitAmount(tx.amount).decimal }}</span>
                PEPE
              </span>
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" role="img" aria-label="Sent" focusable="false">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
              </svg>
              <span class="sr-only">Sent</span>
            </div>
          </div>
        </div>
      </div>

      <div v-if="transactions.length === 0" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
        No transactions found for this address
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="totalTransactions > currentPerPage || after" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
      <div class="flex justify-end">
        <div class="flex items-center gap-2">
          <!-- Newer (Reset/Back) -->
          <button
            @click="navigateNewer"
            :disabled="!after"
            class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer inline-flex items-center gap-1 transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" role="img" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Newer
          </button>

          <!-- Older -->
          <button
            @click="navigateOlder"
            :disabled="!nextAfter"
            class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer inline-flex items-center gap-1 transition-colors"
          >
            Older
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" role="img" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Timestamp from './Timestamp.vue';

export default {
  name: 'AddressTransactions',
  components: {
    Timestamp
  },
  data() {
    return {
      transactions: [],
      currentPerPage: 25,
      totalTransactions: 0,
      nextAfter: null,
      after: null,
      address: '',
      txRoute: '',
      perPageOptions: [25, 50, 100],
      pollingInterval: null
    }
  },
  mounted() {
    this.initializeFromElement();
    if (!this.after) {
      this.startPolling();
    }
    document.addEventListener('visibilitychange', this.handleVisibilityChange);
  },
  beforeUnmount() {
    this.stopPolling();
    document.removeEventListener('visibilitychange', this.handleVisibilityChange);
  },
  methods: {
    initializeFromElement() {
      try {
        const dataScript = document.getElementById('address-transactions-data');
        if (!dataScript) return;

        const data = JSON.parse(dataScript.textContent);

        this.address = data.address || '';
        this.txRoute = data.txRoute || '';
        this.transactions = data.transactions || [];
        this.currentPerPage = data.perPage || 25;
        this.totalTransactions = data.total || 0;
        this.nextAfter = data.nextAfter || null;
        this.after = data.after || null;

        localStorage.setItem('address_tx_per_page', this.currentPerPage);

      } catch (error) {
        console.error('AddressTransactions: Error parsing data', error);
      }
    },
    async fetchTransactions() {
      try {
        // Only fetch if we are on the first page
        if (this.after) return;

        const response = await fetch(`/api/address/${this.address}/txs`);
        if (!response.ok) throw new Error('Network response was not ok');
        const data = await response.json();
        
        // We only update if we are on the first page
        this.transactions = data;
      } catch (error) {
        console.error('AddressTransactions: Error fetching transactions', error);
      }
    },
    startPolling() {
      if (this.pollingInterval || this.after) return;
      this.pollingInterval = setInterval(() => {
        this.fetchTransactions();
      }, 60000);
    },
    stopPolling() {
      if (this.pollingInterval) {
        clearInterval(this.pollingInterval);
        this.pollingInterval = null;
      }
    },
    handleVisibilityChange() {
      if (document.hidden) {
        this.stopPolling();
      } else if (!this.after) {
        this.fetchTransactions();
        this.startPolling();
      }
    },
    navigateOlder() {
      if (!this.nextAfter) return;
      this.updateUrl({ after: this.nextAfter, page: null });
    },
    navigateNewer() {
      if (window.history.length > 1 && document.referrer.includes(window.location.pathname)) {
        window.history.back();
      } else {
        this.updateUrl({ after: null, page: null });
      }
    },
    rememberPerPage(perPage) {
      try {
        if (this.perPageOptions.includes(perPage)) {
          localStorage.setItem('address_tx_per_page', perPage);
        }
      } catch (_) {
        // ignore storage errors
      }
    },
    updateUrl(params) {
      const url = new URL(window.location);
      Object.keys(params).forEach(key => {
        if (params[key] === null) {
          url.searchParams.delete(key);
        } else {
          url.searchParams.set(key, params[key]);
        }
      });
      window.location.href = url.toString();
    },
    formatAmount(amount) {
      return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 6
      }).format(amount);
    },
    splitAmount(amount) {
      const formatted = this.formatAmount(amount);
      const parts = formatted.split('.');
      return {
        whole: parts[0],
        decimal: parts[1] ? '.' + parts[1] : ''
      };
    },
    buildUrlForPerPage(perPage) {
      const url = new URL(window.location);
      url.searchParams.set('per_page', perPage);
      url.searchParams.delete('after');
      url.searchParams.delete('page');
      return url.toString();
    }
  }
}
</script>
