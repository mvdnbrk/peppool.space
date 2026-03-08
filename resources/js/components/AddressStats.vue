<template>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <!-- Transactions Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" role="status" aria-label="Transactions">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
        </div>
        <div class="ml-5">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Transactions</p>
          <div class="text-lg font-bold text-gray-900 dark:text-white">
            {{ stats.txCount.toLocaleString() }}
          </div>
        </div>
      </div>
    </div>

    <!-- Total PEPE Received Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" role="status" aria-label="Total PEPE Received">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
            </svg>
          </div>
        </div>
        <div class="ml-5">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total PEPE Received</p>
          <div class="text-lg font-bold text-gray-900 dark:text-white">
            <span class="inline-flex flex-row flex-nowrap items-baseline text-green-600"><span class="shrink-0">{{ formatPepe(stats.totalReceived).whole }}</span><span class="text-[0.85em] text-gray-500 dark:text-gray-400 font-normal shrink">{{ formatPepe(stats.totalReceived).decimal }}</span></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Total PEPE Sent Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" role="status" aria-label="Total PEPE Sent">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <div class="w-10 h-10 bg-red-500 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
            </svg>
          </div>
        </div>
        <div class="ml-5">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total PEPE Sent</p>
          <div class="text-lg font-bold text-gray-900 dark:text-white">
            <span class="inline-flex flex-row flex-nowrap items-baseline"><span class="shrink-0">{{ formatPepe(stats.totalSent).whole }}</span><span class="text-[0.85em] text-gray-500 dark:text-gray-400 font-normal shrink">{{ formatPepe(stats.totalSent).decimal }}</span></span>
          </div>
        </div>
      </div>
    </div>

    <!-- PEPE Balance Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" role="status" aria-label="PEPE Balance">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 460.6 460.6">
              <path fill="currentColor" d="M363.313,204.043c-4.854,29.719-18.681,50.006-41.479,60.86c-10.275,4.892-20.082,7.546-29.38,7.97 c-12.479,0.507-30.858-1.219-55.124-5.179l-34.276-5.593l-16.042,98.2l-52.559-8.584l26.452-161.958l-26.989-4.409l5.398-33.026 l26.988,4.408l10.674-65.352l84.354,13.777c31.838,5.205,52.461,9.914,61.869,14.116c16.314,7.288,27.959,18.918,34.957,34.887 C364.617,168.953,366.332,185.58,363.313,204.043z M309.047,195.905c1.479-9.117,0.07-17.243-4.227-24.392 c-4.299-7.137-10.82-12.278-19.551-15.409c-6.195-2.222-18.705-4.879-37.531-7.949l-25.393-4.15l-3.479,21.313l46.701,7.631 l-5.391,33.026l-46.708-7.633l-3.188,19.54l28.764,4.696c15.39,2.512,26.01,3.695,31.867,3.563 c12.57-0.256,22.212-3.907,28.957-10.947C304.705,210.154,307.766,203.724,309.047,195.905z"/>
            </svg>
          </div>
        </div>
        <div class="ml-5">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">PEPE Balance</p>
          <div class="text-lg font-bold text-gray-900 dark:text-white">
            <span class="inline-flex flex-row flex-nowrap items-baseline"><span class="shrink-0">{{ formatPepe(stats.balance).whole }}</span><span class="text-[0.85em] text-gray-500 dark:text-gray-400 font-normal shrink">{{ formatPepe(stats.balance).decimal }}</span></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AddressStats',
  props: {
    address: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      stats: {
        txCount: 0,
        totalReceived: 0,
        totalSent: 0,
        balance: 0
      },
      pollingInterval: null
    }
  },
  mounted() {
    this.initializeFromElement();
    this.startPolling();
    document.addEventListener('visibilitychange', this.handleVisibilityChange);
  },
  beforeUnmount() {
    this.stopPolling();
    document.removeEventListener('visibilitychange', this.handleVisibilityChange);
  },
  methods: {
    initializeFromElement() {
      try {
        const dataScript = document.getElementById('address-stats-data');
        if (!dataScript) return;
        
        const data = JSON.parse(dataScript.textContent);
        this.stats = {
          txCount: data.txCount || 0,
          totalReceived: data.totalReceived || 0,
          totalSent: data.totalSent || 0,
          balance: data.balance || 0
        };
      } catch (error) {
        console.error('AddressStats: Error parsing data', error);
      }
    },
    async fetchStats() {
      try {
        const response = await fetch(`/api/address/${this.address}`);
        if (!response.ok) throw new Error('Network response was not ok');
        const data = await response.json();
        
        this.stats = {
          txCount: (data.chain_stats.tx_count || 0) + (data.mempool_stats.tx_count || 0),
          totalReceived: (data.chain_stats.funded_txo_sum || 0) + (data.mempool_stats.funded_txo_sum || 0),
          totalSent: (data.chain_stats.spent_txo_sum || 0) + (data.mempool_stats.spent_txo_sum || 0),
          balance: (data.chain_stats.funded_txo_sum || 0) - (data.chain_stats.spent_txo_sum || 0) + 
                   (data.mempool_stats.funded_txo_sum || 0) - (data.mempool_stats.spent_txo_sum || 0)
        };

        // Emit update so transactions component can pick it up if needed
        this.$emit('stats-updated', this.stats);
      } catch (error) {
        console.error('AddressStats: Error fetching stats', error);
      }
    },
    startPolling() {
      if (this.pollingInterval) return;
      this.pollingInterval = setInterval(() => {
        this.fetchStats();
      }, 60000);
    },
    stopPolling() {
      if (this.pollingInterval) {
        clearInterval(this.pollingInterval);
        this.pollingInterval = null;
      }
    },
    formatPepe(amount) {
      const formatted = new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 6
      }).format(amount);
      const clean = formatted.replace(/0+$/, '').replace(/\.$/, '.00');
      const parts = clean.split('.');
      return { whole: parts[0], decimal: parts[1] ? '.' + parts[1] : '' };
    },
    handleVisibilityChange() {
      if (document.hidden) {
        this.stopPolling();
      } else {
        this.fetchStats();
        this.startPolling();
      }
    }
  }
}
</script>
