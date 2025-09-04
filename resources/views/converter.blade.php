<x-layout :title="$title" :og_image="$og_image" :og_description="$description">
    <div class="mb-6 md:mb-8 text-gray-600 dark:text-gray-300">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-200 mb-2">PEPE Currency Converter</h1>
        <p class="text-sm md:text-base text-gray-500">Convert Pepecoin (PEPE) to USD, EUR and other currencies with real-time rates.</p>
    </div>

    <!-- Main Converter Card -->
    <div class="max-w-2xl lg:max-w-none mx-auto"
         x-data="converter({{ $price->usd ?? 0 }}, {{ $price->eur ?? 0 }})">

        <!-- Quick Amount Buttons -->
        <div class="mb-6" x-show="isPepeToFiat">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Quick amounts:</p>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2">
                <template x-for="amount in quickAmounts" :key="amount">
                    <button
                        @click="setQuickAmount(amount)"
                        class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors text-sm font-medium"
                        x-text="formatNumber(amount) + ' PEPE'"
                    ></button>
                </template>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-xl border border-green-100 dark:border-gray-700 p-6 md:p-8">

            <!-- From Section -->
            <div class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 md:p-6 shadow-sm border border-gray-200 dark:border-gray-600">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From</label>
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center space-x-2 bg-green-100 dark:bg-green-900/30 px-3 py-2 rounded-lg">
                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm" x-text="isPepeToFiat ? '₱' : currencySymbol(selectedCurrency)"></div>
                            <span class="font-semibold text-green-700 dark:text-green-300" x-text="isPepeToFiat ? 'PEPE' : selectedCurrency.toUpperCase()"></span>
                        </div>
                        <input
                            type="text"
                            x-model="baseDisplay"
                            @input="onBaseInput($event)"
                            :placeholder="isPepeToFiat ? 'Enter PEPE amount' : 'Enter amount'"
                            class="flex-1 text-2xl md:text-3xl font-bold bg-transparent border-none outline-none text-gray-900 dark:text-gray-100 placeholder-gray-400"
                            inputmode="decimal"
                            autocomplete="off"
                            spellcheck="false"
                        >
                    </div>
                </div>

                <!-- Swap Button -->
                <div class="flex justify-center">
                    <button
                        @click="swapCurrencies()"
                        class="p-3 bg-green-600 hover:bg-green-700 text-white rounded-full shadow-lg transition-all duration-200 hover:scale-110 focus:outline-none focus:ring-4 focus:ring-green-200 dark:focus:ring-green-800"
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
                        <div class="flex items-center space-x-2 bg-green-100 dark:bg-green-900/30 px-3 py-2 rounded-lg" x-show="!isPepeToFiat">
                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm">₱</div>
                            <span class="font-semibold text-green-700 dark:text-green-300">PEPE</span>
                        </div>
                        <select
                            x-show="isPepeToFiat"
                            x-model="selectedCurrency"
                            @change="updateConversion()"
                            class="bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-gray-900 dark:text-gray-100 font-semibold focus:outline-none focus:ring-2 focus:ring-green-500"
                        >
                            <option value="usd">🇺🇸 USD</option>
                            <option value="eur">🇪🇺 EUR</option>
                        </select>
                        <div class="flex-1 text-2xl md:text-3xl font-bold text-gray-900 dark:text-gray-100" x-text="getOutputText()">
                            $0.00
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Rate Display -->
            <div class="mt-6 p-4 bg-green-100 dark:bg-green-900/20 rounded-lg">
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Current Rate</p>
                    <p class="text-lg font-semibold text-green-700 dark:text-green-300">
                        1 PEPE = <span x-text="formatCurrency(getCurrentRate(), selectedCurrency)"></span>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Last updated: <span x-text="new Date().toLocaleTimeString()"></span>
                    </p>
                </div>
            </div>

        </div>

    </div>

    <script>
        function converter(usdRate, eurRate) {
            return {
                isPepeToFiat: true,
                pepeAmount: 1000000,
                fiatAmount: 0,
                baseDisplay: '',
                selectedCurrency: 'usd',
                convertedAmount: 0,
                rates: {
                    usd: usdRate,
                    eur: eurRate
                },
                quickAmounts: [100, 1000, 100000, 500000, 1000000, 10000000],

                init() {
                    this.updateConversion();
                    this.baseDisplay = this.formatPepeDisplay(this.pepeAmount);
                },

                updateConversion() {
                    const rate = this.getCurrentRate();
                    this.convertedAmount = this.isPepeToFiat
                        ? (this.pepeAmount * rate)
                        : (rate > 0 ? this.fiatAmount / rate : 0);
                },

                getCurrentRate() {
                    return this.rates[this.selectedCurrency] || 0;
                },

                swapCurrencies() {
                    // Store the current input value (what user actually entered)
                    const currentInputValue = this.isPepeToFiat ? this.pepeAmount : this.fiatAmount;

                    // Toggle conversion direction between PEPE <-> Fiat
                    this.isPepeToFiat = !this.isPepeToFiat;

                    // Set the current input value as the new input for the swapped direction
                    if (this.isPepeToFiat) {
                        // Now converting PEPE to fiat, keep the same input value as PEPE
                        this.pepeAmount = currentInputValue;
                        this.baseDisplay = this.formatPepeDisplay(this.pepeAmount);
                    } else {
                        // Now converting fiat to PEPE, keep the same input value as fiat
                        this.fiatAmount = currentInputValue;
                        this.baseDisplay = this.formatPepeDisplay(this.fiatAmount);
                    }

                    this.updateConversion();
                },

                setQuickAmount(amount) {
                    // Only applicable when PEPE is the base
                    this.pepeAmount = amount;
                    this.baseDisplay = this.formatPepeDisplay(this.pepeAmount);
                    this.updateConversion();
                },

                onBaseInput(event) {
                    const raw = (event.target.value || '').toString();
                    const cleaned = raw.replace(/,/g, '');
                    const parts = cleaned.split('.');
                    const normalized = parts.length > 1 ? parts[0] + '.' + parts.slice(1).join('') : cleaned;
                    const numeric = Number(normalized);
                    if (this.isPepeToFiat) {
                        this.pepeAmount = isNaN(numeric) ? 0 : numeric;
                    } else {
                        this.fiatAmount = isNaN(numeric) ? 0 : numeric;
                    }
                    this.baseDisplay = this.formatPepeDisplay(normalized);
                    this.updateConversion();
                },

                formatPepeDisplay(value) {
                    // Keep decimals but add thousand separators to integer part
                    const str = (value ?? '').toString();
                    if (str === '') return '';
                    const [intPart, decPart] = str.split('.');
                    const intNum = intPart.replace(/\D/g, '');
                    const withSep = intNum === '' ? '0' : intNum.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    return decPart !== undefined && decPart !== '' ? `${withSep}.${decPart.replace(/\D/g, '')}` : withSep;
                },

                formatCurrency(amount, currency) {
                    const formatter = new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: currency.toUpperCase(),
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 8
                    });
                    return formatter.format(amount);
                },

                formatNumber(number) {
                    return new Intl.NumberFormat('en-US').format(number);
                },

                currencySymbol(code) {
                    switch ((code || '').toLowerCase()) {
                        case 'usd': return '$';
                        case 'eur': return '€';
                        default: return code ? code.toUpperCase().slice(0,1) : '?';
                    }
                },

                getOutputText() {
                    if (this.isPepeToFiat) {
                        return this.formatCurrency(this.convertedAmount, this.selectedCurrency);
                    }
                    return this.formatNumber(this.convertedAmount) + ' PEPE';
                }
            }
        }
    </script>
</x-layout>
