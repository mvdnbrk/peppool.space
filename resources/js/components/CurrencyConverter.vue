<template>
  <div class="max-w-2xl lg:max-w-none mx-auto">
    <!-- Quick Amount Buttons -->
    <div class="mb-6" v-show="isPepeToFiat">
      <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Quick amounts:</p>
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2">
        <button
          v-for="amount in quickAmounts"
          :key="amount"
          @click="setQuickAmount(amount)"
          class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors text-sm font-medium"
        >
          {{ formatNumber(amount) }} PEPE
        </button>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 md:p-8">
      <!-- From Section -->
      <div class="space-y-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 md:p-6 shadow-sm border border-gray-200 dark:border-gray-600">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From</label>
          <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-2 bg-green-100 dark:bg-green-900/30 px-3 py-2 rounded-lg">
              <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                {{ isPepeToFiat ? '₱' : currencySymbol(selectedCurrency) }}
              </div>
              <span class="font-semibold text-green-700 dark:text-green-300">
                {{ isPepeToFiat ? 'PEPE' : selectedCurrency.toUpperCase() }}
              </span>
            </div>
            <input
              type="text"
              v-model="baseDisplay"
              @input="onBaseInput"
              :placeholder="isPepeToFiat ? 'Enter PEPE amount' : 'Enter amount'"
              class="flex-1 text-2xl md:text-3xl font-bold bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 transition-all duration-200"
              inputmode="decimal"
              autocomplete="off"
              spellcheck="false"
            >
          </div>
        </div>

        <!-- Swap Button -->
        <div class="flex justify-center">
          <button
            @click="swapCurrencies"
            class="p-3 bg-green-600 hover:bg-green-700 text-white rounded-full shadow-sm transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-4 focus:ring-green-200 dark:focus:ring-green-800"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
            </svg>
          </button>
        </div>

        <!-- To Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 md:p-6 shadow-sm border border-gray-200 dark:border-gray-600">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To</label>
          <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-2 bg-green-100 dark:bg-green-900/30 px-3 py-2 rounded-lg" v-show="!isPepeToFiat">
              <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm">₱</div>
              <span class="font-semibold text-green-700 dark:text-green-300">PEPE</span>
            </div>
            <select
              v-show="isPepeToFiat"
              v-model="selectedCurrency"
              @change="updateConversion"
              class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-gray-900 dark:text-gray-100 font-semibold focus:outline-none focus:ring-2 focus:ring-green-500"
            >
              <option value="usd">🇺🇸 USD</option>
              <option value="eur">🇪🇺 EUR</option>
            </select>
            <input
              type="text"
              v-model="targetDisplay"
              @input="onTargetInput"
              :placeholder="isPepeToFiat ? 'Converted amount' : 'Enter PEPE amount'"
              class="flex-1 text-2xl md:text-3xl font-bold bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-800 transition-all duration-200"
              inputmode="decimal"
              autocomplete="off"
              spellcheck="false"
            >
          </div>
        </div>
      </div>

      <!-- Current Rate Display -->
      <div class="mt-6 p-4 bg-green-100 dark:bg-green-900/20 rounded-lg">
        <div class="text-center">
          <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Current Rate</p>
          <p class="text-lg font-semibold text-green-700 dark:text-green-500">
            1 PEPE = {{ formatCurrency(getCurrentRate(), selectedCurrency) }}
          </p>
          <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            Last updated: {{ new Date().toLocaleTimeString() }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CurrencyConverter',
  props: {
    usdRate: {
      type: Number,
      default: 0
    },
    eurRate: {
      type: Number,
      default: 0
    }
  },
  data() {
    return {
      isPepeToFiat: true,
      pepeAmount: 1000000,
      fiatAmount: 0,
      baseDisplay: '',
      targetDisplay: '',
      selectedCurrency: 'usd',
      convertedAmount: 0,
      quickAmounts: [100, 1000, 100000, 500000, 1000000, 10000000]
    }
  },
  computed: {
    rates() {
      return {
        usd: this.usdRate,
        eur: this.eurRate
      }
    }
  },
  mounted() {
    this.updateConversion()
    this.baseDisplay = this.formatPepeDisplay(this.pepeAmount)
    this.updateTargetDisplay()
  },
  methods: {
    updateConversion() {
      const rate = this.getCurrentRate()
      this.convertedAmount = this.isPepeToFiat
        ? (this.pepeAmount * rate)
        : (rate > 0 ? this.fiatAmount / rate : 0)
      this.updateTargetDisplay()
    },

    updateTargetDisplay() {
      if (this.isPepeToFiat) {
        this.targetDisplay = this.formatCurrency(this.convertedAmount, this.selectedCurrency)
      } else {
        this.targetDisplay = this.formatNumber(this.convertedAmount) + ' PEPE'
      }
    },

    getCurrentRate() {
      return this.rates[this.selectedCurrency] || 0
    },

    swapCurrencies() {
      // Toggle conversion direction between PEPE <-> Fiat
      this.isPepeToFiat = !this.isPepeToFiat

      // Swap the display values
      const tempDisplay = this.baseDisplay
      this.baseDisplay = this.targetDisplay.replace(/[$€,]/g, '').replace(' PEPE', '')
      this.targetDisplay = tempDisplay

      // Update the underlying amounts based on the new base input
      if (this.isPepeToFiat) {
        const cleaned = this.baseDisplay.replace(/,/g, '')
        this.pepeAmount = Number(cleaned) || 0
        this.baseDisplay = this.formatPepeDisplay(this.pepeAmount)
      } else {
        const cleaned = this.baseDisplay.replace(/,/g, '')
        this.fiatAmount = Number(cleaned) || 0
        this.baseDisplay = this.formatPepeDisplay(this.fiatAmount)
      }

      this.updateConversion()
    },

    setQuickAmount(amount) {
      // Only applicable when PEPE is the base
      this.pepeAmount = amount
      this.baseDisplay = this.formatPepeDisplay(this.pepeAmount)
      this.updateConversion()
    },

    onBaseInput(event) {
      const raw = (event.target.value || '').toString()
      // Remove commas and keep only digits, decimal point
      const cleaned = raw.replace(/[^0-9.]/g, '')
      const parts = cleaned.split('.')
      const normalized = parts.length > 1 ? parts[0] + '.' + parts.slice(1).join('') : cleaned
      const numeric = Number(normalized)
      
      // Only update if we have a valid number or empty string
      if (normalized === '' || !isNaN(numeric)) {
        if (this.isPepeToFiat) {
          this.pepeAmount = normalized === '' ? 0 : numeric
        } else {
          this.fiatAmount = normalized === '' ? 0 : numeric
        }
        this.baseDisplay = this.formatPepeDisplay(normalized)
        this.updateConversion()
      }
    },

    onTargetInput(event) {
      const raw = (event.target.value || '').toString()
      // Remove currency symbols and PEPE text, keep only digits and decimal point
      const cleaned = raw.replace(/[^0-9.]/g, '')
      const parts = cleaned.split('.')
      const normalized = parts.length > 1 ? parts[0] + '.' + parts.slice(1).join('') : cleaned
      const numeric = Number(normalized)

      // Only update if we have a valid number or empty string
      if (normalized === '' || !isNaN(numeric)) {
        if (this.isPepeToFiat) {
          // Target is fiat, so convert back to PEPE
          this.fiatAmount = normalized === '' ? 0 : numeric
          const rate = this.getCurrentRate()
          this.pepeAmount = rate > 0 ? this.fiatAmount / rate : 0
          this.baseDisplay = this.formatPepeDisplay(this.pepeAmount)
        } else {
          // Target is PEPE, so convert back to fiat
          this.pepeAmount = normalized === '' ? 0 : numeric
          const rate = this.getCurrentRate()
          this.fiatAmount = this.pepeAmount * rate
          this.baseDisplay = this.formatPepeDisplay(this.fiatAmount)
        }

        // Update target display to show formatted version
        this.targetDisplay = this.isPepeToFiat
          ? this.formatCurrency(this.fiatAmount, this.selectedCurrency)
          : this.formatNumber(this.pepeAmount) + ' PEPE'
      }
    },

    formatPepeDisplay(value) {
      // Keep decimals but add thousand separators to integer part
      const str = (value ?? '').toString()
      if (str === '') return ''
      const [intPart, decPart] = str.split('.')
      const intNum = intPart.replace(/\D/g, '')
      const withSep = intNum === '' ? '0' : intNum.replace(/\B(?=(\d{3})+(?!\d))/g, ',')
      return decPart !== undefined && decPart !== '' ? `${withSep}.${decPart.replace(/\D/g, '')}` : withSep
    },

    formatCurrency(amount, currency) {
      const formatter = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency.toUpperCase(),
        minimumFractionDigits: 2,
        maximumFractionDigits: 8
      })
      return formatter.format(amount)
    },

    formatNumber(number) {
      return new Intl.NumberFormat('en-US').format(number)
    },

    currencySymbol(code) {
      switch ((code || '').toLowerCase()) {
        case 'usd': return '$'
        case 'eur': return '€'
        default: return code ? code.toUpperCase().slice(0,1) : '?'
      }
    }
  }
}
</script>
