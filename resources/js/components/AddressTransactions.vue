<template>
  <div class="bg-white dark:bg-gray-900 shadow rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
    <!-- Header -->
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
      <div class="flex flex-col gap-3">
        <!-- Header: Mobile stacked; Desktop 2-col grid [title | controls] with no-wrap controls -->
        <div class="flex flex-col gap-3 sm:grid sm:grid-cols-[1fr_auto] sm:items-center">
          <!-- Title -->
          <h2 class="text-lg font-semibold text-gray-900 dark:text-white sm:min-w-0 truncate">Transactions</h2>
          <!-- Desktop: Tabs + Per Page grouped together, no wrap -->
          <div class="hidden sm:inline-flex sm:items-center sm:justify-end sm:gap-3 sm:flex-nowrap sm:whitespace-nowrap sm:overflow-x-auto sm:max-w-full">
            <div class="inline-flex gap-1 rounded-full bg-gray-950/5 p-1 dark:bg-white/10 shrink-0" role="tablist" aria-orientation="horizontal">
              <button
                v-for="filter in filters"
                :key="filter.key"
                type="button"
                :id="`filter-tab-${filter.key}`"
                role="tab"
                @click="currentFilter = filter.key"
                :aria-selected="currentFilter === filter.key"
                :tabindex="currentFilter === filter.key ? 0 : -1"
                class="group inline-flex items-center justify-center rounded-full px-3 py-1 text-sm font-medium cursor-pointer"
                :class="currentFilter === filter.key 
                  ? 'bg-white ring ring-gray-950/5 dark:bg-gray-600 dark:ring-0 text-gray-900 dark:text-white' 
                  : 'text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white'"
              >
                {{ filter.label }}
              </button>
            </div>
            <div class="inline-flex items-center gap-2 shrink-0">
              <span class="hidden md:inline text-sm text-gray-500 dark:text-gray-400">Per page:</span>
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
          <!-- Mobile Per Page under Title -->
          <div class="flex items-center gap-2 sm:hidden">
            <span class="text-sm text-gray-500 dark:text-gray-400">Per page:</span>
            <div class="flex gap-1 rounded-full bg-gray-950/5 p-1 dark:bg-white/10">
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
        <!-- Tabs for mobile only, below header -->
        <div class="flex flex-col gap-3 sm:hidden mt-1">
          <div class="grid grid-cols-3 gap-1 rounded-full bg-gray-950/5 p-1 dark:bg-white/10" role="tablist" aria-orientation="horizontal">
            <button
              v-for="filter in filters"
              :key="filter.key"
              type="button"
              :id="`filter-tab-${filter.key}`"
              role="tab"
              @click="currentFilter = filter.key"
              :aria-selected="currentFilter === filter.key"
              :tabindex="currentFilter === filter.key ? 0 : -1"
              class="group flex items-center justify-center w-full rounded-full px-3 py-1 text-sm font-medium cursor-pointer"
              :class="currentFilter === filter.key 
                ? 'bg-white ring ring-gray-950/5 dark:bg-gray-600 dark:ring-0 text-gray-900 dark:text-white' 
                : 'text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white'"
            >
              {{ filter.label }}
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Transaction List -->
    <div class="divide-y divide-gray-200 dark:divide-gray-700">
      <div
        v-for="tx in filteredTransactions"
        :key="tx.txid"
        v-show="shouldShowTransaction(tx)"
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
                <span>{{ splitAmount(tx.amount).whole }}</span><span class="text-[0.85em] text-gray-500 dark:text-gray-400">{{ splitAmount(tx.amount).decimal }}</span>
                PEPE
              </span>
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" role="img" aria-label="Received" focusable="false">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
              </svg>
              <span class="sr-only">Received</span>
            </div>
            <div v-else class="flex items-center justify-end text-red-600">
              <span class="text-red-600">
                <span>{{ splitAmount(tx.amount).whole }}</span><span class="text-[0.85em] text-gray-500 dark:text-gray-400">{{ splitAmount(tx.amount).decimal }}</span>
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
      
      <div v-if="filteredTransactions.length === 0" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
        <div v-if="transactions.length === 0">
          No transactions found for this address
        </div>
        <div v-else>
          No {{ currentFilter === 'in' ? 'incoming' : currentFilter === 'out' ? 'outgoing' : '' }} transactions found
        </div>
      </div>
    </div>
    
    <!-- Pagination -->
    <div v-if="totalPages > 1" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm text-gray-700 dark:text-gray-300">
          Showing {{ ((currentPage - 1) * currentPerPage) + 1 }} to {{ Math.min(currentPage * currentPerPage, totalTransactions) }} of {{ totalTransactions }} results
        </div>
        
        <div class="flex items-center gap-1">
          <!-- First Page -->
          <button
            @click="changePage(1)"
            :disabled="currentPage === 1"
            class="px-3 py-2 text-sm rounded border border-gray-300 dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer"
          >
            First
          </button>
          
          <!-- Previous -->
          <button
            @click="changePage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-3 py-2 text-sm rounded border border-gray-300 dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer"
          >
            ‹
          </button>
          
          <!-- Page Numbers -->
          <template v-for="page in visiblePages" :key="page">
            <span v-if="page === '...'" class="px-3 py-2 text-sm text-gray-500">...</span>
            <button
              v-else
              @click="changePage(page)"
              :class="page === currentPage 
                ? 'px-3 py-2 text-sm rounded bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-300 dark:border-green-600 cursor-pointer' 
                : 'px-3 py-2 text-sm rounded border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer'"
            >
              {{ page }}
            </button>
          </template>
          
          <!-- Next -->
          <button
            @click="changePage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="px-3 py-2 text-sm rounded border border-gray-300 dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer"
          >
            ›
          </button>
          
          <!-- Last Page -->
          <button
            @click="changePage(totalPages)"
            :disabled="currentPage === totalPages"
            class="px-3 py-2 text-sm rounded border border-gray-300 dark:border-gray-600 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer"
          >
            Last
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
      currentFilter: 'all',
      currentPerPage: 10,
      currentPage: 1,
      totalTransactions: 0,
      totalPages: 1,
      address: '',
      txRoute: '',
      filters: [
        { key: 'all', label: 'All' },
        { key: 'in', label: 'Incoming' },
        { key: 'out', label: 'Outgoing' }
      ],
      perPageOptions: [10, 25, 50, 100]
    }
  },
  computed: {
    filteredTransactions() {
      if (this.currentFilter === 'all') {
        return this.transactions;
      }
      return this.transactions.filter(tx => {
        if (this.currentFilter === 'in') return tx.is_incoming;
        if (this.currentFilter === 'out') return !tx.is_incoming;
        return true;
      });
    },
    visiblePages() {
      const pages = [];
      const current = this.currentPage;
      const total = this.totalPages;
      
      if (total <= 7) {
        // Show all pages if 7 or fewer
        for (let i = 1; i <= total; i++) {
          pages.push(i);
        }
      } else {
        // Always show first page
        pages.push(1);
        
        if (current > 4) {
          pages.push('...');
        }
        
        // Show pages around current page
        const start = Math.max(2, current - 1);
        const end = Math.min(total - 1, current + 1);
        
        for (let i = start; i <= end; i++) {
          if (i !== 1 && i !== total) {
            pages.push(i);
          }
        }
        
        if (current < total - 3) {
          pages.push('...');
        }
        
        // Always show last page
        if (total > 1) {
          pages.push(total);
        }
      }
      
      return pages;
    }
  },
  mounted() {
    this.initializeFromElement();
  },
  methods: {
    initializeFromElement() {
      try {
        // Get data from JSON script tag
        const dataScript = document.getElementById('address-transactions-data');
        if (!dataScript) {
          console.error('AddressTransactions: Data script not found');
          return;
        }
        
        const data = JSON.parse(dataScript.textContent);
        
        this.address = data.address || '';
        this.txRoute = data.txRoute || '';
        this.transactions = data.transactions || [];
        this.currentPage = data.currentPage || 1;
        this.currentPerPage = data.perPage || 10;
        this.totalTransactions = data.total || 0;
        this.totalPages = data.lastPage || 1;

        // Keep localStorage in sync with current value
        localStorage.setItem('address_tx_per_page', this.currentPerPage);
        
      } catch (error) {
        console.error('AddressTransactions: Error parsing data', error);
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
    shouldShowTransaction(tx) {
      if (this.currentFilter === 'all') return true;
      if (this.currentFilter === 'in') return tx.is_incoming;
      if (this.currentFilter === 'out') return !tx.is_incoming;
      return true;
    },
    changePerPage(perPage) {
      this.currentPerPage = perPage;
      this.updateUrl({ per_page: perPage, page: 1 });
    },
    changePage(page) {
      if (page < 1 || page > this.totalPages) return;
      this.currentPage = page;
      this.updateUrl({ page });
    },
    updateUrl(params) {
      const url = new URL(window.location);
      Object.keys(params).forEach(key => {
        url.searchParams.set(key, params[key]);
      });
      window.location.href = url.toString();
    },
    formatAmount(amount) {
      return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 8
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
      url.searchParams.set('page', 1);
      return url.toString();
    }
  }
}
</script>
