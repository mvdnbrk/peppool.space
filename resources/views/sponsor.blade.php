<x-layout
    title="Sponsor peppool.space"
    og_image="pepecoin-sponsor.png"
    og_description="Support the development and maintenance of peppool.space, the real-time Pepecoin blockchain explorer."
>
    <div class="mb-6 md:mb-8 text-gray-600 dark:text-gray-300">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-200 mb-2">Support the Project</h1>
        <p class="text-sm md:text-base text-gray-500">Your contributions help keep this project running and growing.</p>
    </div>

    <div class="max-w-none space-y-8">
        <!-- Why Sponsor -->
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold mb-4">Why Sponsor?</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                <span class="text-green-800 dark:text-green-600 font-semibold">peppool.space</span> is a free, open-source Pepecoin blockchain explorer built for the community. Your support directly funds the infrastructure, hosting, and ongoing development required to provide a fast, reliable, and real-time experience for everyone.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Platform Fees</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Hosting the Pepecoin node, Electrs indexer, and the web application requires high-performance server infrastructure.</p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Ongoing Development</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Regular updates, new features (like the API), and security maintenance are fueled by your contributions.</p>
                </div>
            </div>
        </div>

        <!-- PEP Donation -->
        <div class="bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-900/30 rounded-lg p-6">
            <h2 class="text-xl font-bold text-green-900 dark:text-green-400 mb-4">Donate with Pepecoin</h2>
            <p class="text-green-800 dark:text-green-300 mb-6">
                Directly support the project by sending PEP to the address below.
            </p>

            <div class="bg-white dark:bg-gray-900 p-4 rounded-lg border border-green-200 dark:border-green-800">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wider">
                    PEP Donation Address
                </h3>
                <el-copyable id="donation-address" class="hidden">PbvihBLgz6cFJnhYscevB4n3o85faXPG7D</el-copyable>
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <code class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded px-3 py-3 text-sm md:text-base font-mono text-gray-800 dark:text-gray-200 whitespace-nowrap overflow-x-auto flex-1 w-full text-center sm:text-left">
                        PbvihBLgz6cFJnhYscevB4n3o85faXPG7D
                    </code>
                    <button type="button" command="--copy" commandfor="donation-address" class="w-full sm:w-auto group relative inline-flex shrink-0 cursor-pointer items-center justify-center px-6 py-3 bg-green-700 hover:bg-green-800 text-white rounded-lg transition-colors text-sm font-bold uppercase tracking-wide">
                        <span class="group-data-[copied]:hidden">Copy Address</span>
                        <span class="hidden group-data-[copied]:inline">Copied!</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- External Sponsors -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="https://github.com/sponsors/mvdnbrk" target="_blank" rel="noopener" class="flex items-center gap-4 p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-pink-500 dark:hover:border-pink-500 transition-colors group">
                <div class="p-3 bg-pink-100 dark:bg-pink-900/30 rounded-full text-pink-600">
                    <x-icon-github class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-pink-600 transition-colors">GitHub Sponsors</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Monthly or one-time sponsorship via GitHub.</p>
                </div>
            </a>

            <a href="https://thanks.dev/u/gh/mvdnbrk" target="_blank" rel="noopener" class="flex items-center gap-4 p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-blue-500 dark:hover:border-blue-500 transition-colors group">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full text-blue-600">
                    <x-icon-fathom class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 transition-colors">thanks.dev</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Support open-source contributors directly.</p>
                </div>
            </a>
        </div>

        <!-- DigitalOcean Referral -->
        <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/30 rounded-lg p-6">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <div class="hidden md:flex shrink-0 p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-blue-100 dark:border-blue-900/50">
                    <x-icon-digitalocean class="w-12 h-12 text-[#0080FF]" />
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-blue-900 dark:text-blue-400 mb-2">Support via DigitalOcean</h2>
                    <p class="text-blue-800 dark:text-blue-300 font-medium mb-2">Give $200, Get $25</p>
                    <p class="text-sm text-blue-700 dark:text-blue-400/80 mb-4">
                        Everyone you refer gets $200 in credit over 60 days. Once they’ve spent $25 with us, you'll get $25. There is no limit to the amount of credit you can earn through referrals.
                    </p>
                    <a href="{{ $digitalOceanUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#0080FF] hover:bg-[#0066CC] text-white rounded-lg transition-colors font-semibold text-sm">
                        <span>Get $200 credit on DigitalOcean</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Fathom Analytics -->
        <div class="bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <div class="flex flex-col lg:flex-row gap-8">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-4">
                        <x-icon-fathom class="w-8 h-8 text-gray-900 dark:text-white" />
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Ditch Google Analytics</h2>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        At Fathom, we strongly believe that analytics tools should be insightful, not invasive. This is why we created a privacy-focused, simple software tool that offers valuable insights without the complexity.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 font-semibold text-gray-900 dark:text-gray-100">
                                <x-icon-check-circle-fill class="w-4 h-4 text-green-500" />
                                <span>Get setup in minutes</span>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400">Our script is a single line of code that works with any website, CMS or framework.</p>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 font-semibold text-gray-900 dark:text-gray-100">
                                <x-icon-check-circle-fill class="w-4 h-4 text-green-500" />
                                <span>Comply with privacy laws</span>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400">Fully compliant with GDPR, CCPA, ePrivacy, PECR and more.</p>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 font-semibold text-gray-900 dark:text-gray-100">
                                <x-icon-check-circle-fill class="w-4 h-4 text-green-500" />
                                <span>See more accurate data</span>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400">Blocks bots, scrapers and spam traffic—showing you only real, human visits.</p>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 font-semibold text-gray-900 dark:text-gray-100">
                                <x-icon-check-circle-fill class="w-4 h-4 text-green-500" />
                                <span>No cookie banners</span>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400">Anonymizes IP addresses and other visitor data without using cookies.</p>
                        </div>
                    </div>
                </div>
                <div class="lg:w-72 shrink-0 flex flex-col justify-center gap-4">
                    <div class="p-6 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 text-center">
                        <x-icon-pizza-ninjas-pepe class="w-16 h-16 mx-auto mb-4" />
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-4">Join the movement</p>
                        <a href="{{ $fathomUrl }}" target="_blank" rel="noopener" class="inline-flex w-full items-center justify-center gap-2 px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors font-bold text-xs uppercase tracking-widest">
                            Try Fathom
                        </a>
                    </div>
                    <p class="text-[10px] text-center text-gray-400 uppercase tracking-widest">European analytics cat approved</p>
                </div>
            </div>
        </div>

        <div class="text-center py-8">
            <p class="text-gray-500 dark:text-gray-400 italic">Make something cool :)</p>
        </div>
    </div>
</x-layout>
