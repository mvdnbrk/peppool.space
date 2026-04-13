<template>
  <el-dropdown v-if="address">
    <button
      type="button"
      class="group flex items-center space-x-1 sm:space-x-2 bg-gradient-to-r from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 px-2 sm:px-3 py-1 sm:py-1.5 rounded-full transition-all duration-200 border border-green-200 hover:border-green-300 cursor-pointer"
    >
      <span class="text-green-700 group-hover:text-green-800 font-medium text-xs sm:text-sm font-mono">
        {{ address.slice(0, 4) }}...{{ address.slice(-4) }}
      </span>
    </button>
    <el-menu anchor="bottom-end" popover class="mt-1 w-44 rounded-lg bg-white shadow-lg ring-1 ring-gray-200 p-1">
      <a :href="`/address/${address}`" class="block w-full px-3 py-2 text-left text-sm text-gray-700 rounded-md hover:bg-gray-100 focus:bg-gray-100">View address</a>
      <button type="button" class="block w-full px-3 py-2 text-left text-sm text-red-600 rounded-md hover:bg-red-50 focus:bg-red-50 cursor-pointer" @click="disconnect">Disconnect</button>
    </el-menu>
  </el-dropdown>
  <button
    v-else-if="installed"
    @click="connect"
    class="group flex items-center space-x-1 sm:space-x-2 bg-gradient-to-r from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 px-2 sm:px-3 py-1 sm:py-1.5 rounded-full transition-all duration-200 border border-green-200 hover:border-green-300 cursor-pointer"
  >
    <span class="text-green-700 group-hover:text-green-800 font-medium text-xs sm:text-sm">Connect</span>
  </button>
</template>

<script>
export default {
  name: 'WalletConnect',
  data() {
    return {
      address: null,
      installed: false,
      provider: null,
    }
  },
  mounted() {
    this.provider = this.getProvider()

    if (this.provider) {
      this.installed = true
      this.checkExistingConnection()
    } else {
      window.addEventListener('pep_providers#peppool', (event) => {
        this.provider = event.detail.provider
        this.installed = true
        this.checkExistingConnection()
      })
    }
  },
  methods: {
    getProvider() {
      if (typeof window === 'undefined') return null
      const providers = window.pep_providers || []
      return providers.find(p => p.id === 'peppool') || null
    },
    async checkExistingConnection() {
      try {
        const accounts = await this.provider.request('getAccounts')
        if (accounts && accounts.length > 0) {
          this.address = accounts[0]
        }
      } catch {
        // Not connected
      }
    },
    async connect() {
      try {
        const accounts = await this.provider.request('wallet_connect')
        if (accounts && accounts.length > 0) {
          this.address = accounts[0]
        }
      } catch {
        // User rejected
      }
    },
    async disconnect() {
      try {
        await this.provider.request('wallet_disconnect')
      } catch {
        // Ignore errors
      }
      this.address = null
    },
  }
}
</script>
