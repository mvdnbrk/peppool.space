<x-layout title="Peppool Wallet: The Pepecoin wallet for everyone. (coming soon)" og_image="peppol-wallet.png" og_description="The Pepecoin wallet for everyone. Coming soon.">
    <div class="max-w-none space-y-8 lg:space-y-12">
        <!-- Hero Section -->
        <div class="relative bg-white dark:bg-gray-900 shadow-2xs rounded-[2rem] border border-gray-200 dark:border-gray-700 my-16 md:my-24 lg:my-32">
            <div class="grid grid-cols-1 md:grid-cols-12 items-center">
                <!-- Text Content -->
                <div class="md:col-span-7 p-8 md:p-12 lg:p-16 xl:p-20 space-y-10">
                    <div class="space-y-6">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-bold uppercase tracking-wider">
                            Coming Soon
                        </div>
                        
                        <div class="space-y-4">
                            <h1 class="text-4xl lg:text-6xl font-extrabold text-gray-900 dark:text-gray-100 leading-[1.1] tracking-tight">
                                The <span class="text-green-700">Pepecoin</span> wallet for everyone.
                            </h1>
                            <div class="space-y-2 max-w-lg">
                                <p class="text-lg lg:text-xl text-gray-600 dark:text-gray-400 leading-relaxed">
                                    We're building a powerful, easy-to-use browser extension wallet for the Pepecoin network.
                                </p>
                                <p class="text-lg lg:text-xl text-gray-600 dark:text-gray-400 leading-relaxed">
                                    Manage your PEP, and more—all from your browser.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        <a href="{{ $socials->github_url }}/peppool-wallet" target="_blank" rel="noopener" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-900 hover:bg-black text-white rounded-xl transition-all duration-300 font-bold shadow-[0_10px_20px_-5px_rgba(0,0,0,0.3)] hover:shadow-[0_15px_30px_-5px_rgba(0,0,0,0.4)] hover:-translate-y-1 active:scale-95">
                            <x-icon-github class="w-5 h-5" />
                            <span>View on GitHub</span>
                        </a>
                        <a href="#join-waitlist" class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2 group transition-colors hover:text-green-700 dark:hover:text-green-500 tracking-wide uppercase text-sm">
                            Join the waitlist
                            <span aria-hidden="true" class="transition-transform group-hover:translate-x-1">→</span>
                        </a>
                    </div>
                </div>
                
                <!-- Eye Catcher (App View) -->
                <div class="md:col-span-5 p-12 md:p-0 flex items-center justify-center md:justify-end md:pr-12 lg:pr-20">
                    <div class="relative w-full max-w-[260px] lg:max-w-[300px] md:scale-110 lg:scale-125 transform transition-all duration-700 z-20 group">
                        <!-- macOS Frame -->
                        <div class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-[0_40px_80px_-15px_rgba(0,0,0,0.25)] dark:shadow-[0_40px_80px_-15px_rgba(0,0,0,0.7)] border border-gray-200/50 dark:border-gray-700/50 transition-all duration-700 hover:-translate-y-6 hover:rotate-1 hover:shadow-[0_60px_120px_-15px_rgba(0,0,0,0.35)] dark:hover:shadow-[0_60px_120px_-15px_rgba(0,0,0,0.8)]">
                            <!-- macOS Title Bar -->
                            <div class="bg-gray-100/50 dark:bg-gray-800/50 backdrop-blur-md px-4 py-3 flex items-center gap-1.5 border-b border-gray-200/50 dark:border-gray-700/50">
                                <div class="w-2.5 h-2.5 rounded-full bg-[#ff5f56] shadow-inner"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-[#ffbd2e] shadow-inner"></div>
                                <div class="w-2.5 h-2.5 rounded-full bg-[#27c93f] shadow-inner"></div>
                            </div>
                            <!-- App Content -->
                            <div class="relative">
                                <img src="{{ cdn_asset('wallet-preview/0-welcome.png') }}" alt="Peppool Wallet Welcome Screen" class="w-full h-auto transition-transform duration-700 group-hover:scale-[1.03]">
                                <div class="absolute inset-0 bg-gradient-to-tr from-white/5 to-transparent pointer-events-none"></div>
                            </div>
                        </div>
                        
                        <!-- Floating decoration blobs -->
                        <div class="absolute -top-10 -right-10 w-24 h-24 bg-green-500/10 blur-3xl rounded-full -z-10 animate-pulse"></div>
                        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-emerald-500/10 blur-3xl rounded-full -z-10 animate-pulse" style="animation-delay: 1s"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-900 shadow-2xs rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center text-green-600 mb-4">
                    <x-icon-computer-desktop class="w-6 h-6" />
                </div>
                <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-2">Chrome Browser Extension</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">The native Layer 1 Pepecoin experience<br>right in your browser.</p>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-2xs rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center text-green-600 mb-4">
                    <x-icon-lock class="w-6 h-6" />
                </div>
                <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-2">Secure & Open</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Fully open-source and non-custodial.<br>You are always in control of your private keys.</p>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-2xs rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center text-green-600 mb-4">
                    <x-icon-sparkles class="w-6 h-6" />
                </div>
                <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-2">Easy to Use</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Designed with a focus on user experience,<br>making it the perfect wallet for everyone.</p>
            </div>
        </div>

        <!-- Screenshots -->
        <div class="mx-auto mt-8 max-w-7xl px-6 sm:mt-16 lg:px-8">
            <div class="relative mb-10">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-200 dark:border-gray-800"></div>
                </div>
                <div class="relative flex justify-center">
                    <h2 class="bg-gray-50 dark:bg-gray-900 px-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Sneak peek of what's coming
                    </h2>
                </div>
            </div>
            <div class="mx-auto grid max-w-lg grid-cols-2 items-center gap-x-8 gap-y-10 sm:max-w-xl sm:grid-cols-3 sm:gap-x-10 lg:mx-0 lg:max-w-none lg:grid-cols-5">
                <a data-fslightbox="gallery" href="{{ cdn_asset('wallet-preview/1-dashboard.png') }}" class="group relative">
                    <img src="{{ cdn_asset('wallet-preview/1-dashboard-frame.png') }}" alt="Dashboard" class="max-h-32 w-full object-contain transition-transform duration-300 group-hover:scale-110">
                    <p class="mt-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity">Dashboard</p>
                </a>
                <a data-fslightbox="gallery" href="{{ cdn_asset('wallet-preview/2-send.png') }}" class="group relative">
                    <img src="{{ cdn_asset('wallet-preview/2-send-frame.png') }}" alt="Send" class="max-h-32 w-full object-contain transition-transform duration-300 group-hover:scale-110">
                    <p class="mt-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity">Send PEP</p>
                </a>
                <a data-fslightbox="gallery" href="{{ cdn_asset('wallet-preview/3-receive.png') }}" class="group relative">
                    <img src="{{ cdn_asset('wallet-preview/3-receive-frame.png') }}" alt="Receive" class="max-h-32 w-full object-contain transition-transform duration-300 group-hover:scale-110">
                    <p class="mt-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity">Receive</p>
                </a>
                <a data-fslightbox="gallery" href="{{ cdn_asset('wallet-preview/4-settings.png') }}" class="group relative">
                    <img src="{{ cdn_asset('wallet-preview/4-settings-frame.png') }}" alt="Settings" class="max-h-32 w-full object-contain transition-transform duration-300 group-hover:scale-110">
                    <p class="mt-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity">Settings</p>
                </a>
                <a data-fslightbox="gallery" href="{{ cdn_asset('wallet-preview/5-preferences.png') }}" class="group relative">
                    <img src="{{ cdn_asset('wallet-preview/5-preferences-frame.png') }}" alt="Preferences" class="max-h-32 w-full object-contain transition-transform duration-300 group-hover:scale-110">
                    <p class="mt-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity">Preferences</p>
                </a>
            </div>
        </div>

        <!-- Join the Waitlist CTA -->
        <div id="join-waitlist" class="bg-white dark:bg-gray-900 shadow-2xs rounded-3xl border border-gray-200 dark:border-gray-700 px-8 py-10 md:px-12 md:py-12 flex flex-col lg:flex-row items-center justify-between gap-8 pb-12">
            <div class="max-w-xl text-center lg:text-left">
                <p class="font-bold text-gray-600 dark:text-gray-400">
                    Get notified when we launch the Peppool Wallet browser extension.
                </p>
            </div>

            <div 
                data-vue="waitlist-form" 
                data-props='@json(['apiUrl' => route('api.wallet.waitlist')])'
                class="w-full lg:w-auto min-w-[320px] sm:min-w-[440px]"
            >
                <!-- Fallback if JS is disabled -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="email" placeholder="Enter your email" disabled class="flex-grow px-5 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-gray-100 placeholder-gray-400 outline-none">
                    <button disabled class="px-8 py-3 bg-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-700/20 opacity-50 tracking-wider uppercase text-xs">Join Waitlist</button>
                </div>
            </div>
        </div>
    </div>
</x-layout>
