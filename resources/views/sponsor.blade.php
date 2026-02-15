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
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100">Why Sponsor peppool.space?</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                <span class="text-green-800 dark:text-green-600 font-semibold">peppool.space</span> is a free, open-source Pepecoin blockchain explorer built for the community.
            </p>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                Your support directly funds the infrastructure, hosting, and ongoing development required to provide a fast, reliable, and real-time experience for everyone.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Platform Fees</h3>
                    <p class="text-gray-600 dark:text-gray-400">Hosting the Pepecoin node, Electrs indexer, and the web application requires high-performance server infrastructure.</p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Ongoing Development</h3>
                    <p class="text-gray-600 dark:text-gray-400">Regular updates, new features (like the API), and security maintenance are fueled by your contributions.</p>
                </div>
            </div>
        </div>

        <!-- PEP Donation -->
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Donate with Pepecoin</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
                Directly support the project by sending PEP to the address below.
            </p>

            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wider">
                    PEP Donation Address
                </h3>
                <el-copyable id="donation-address" class="hidden">PbvihBLgz6cFJnhYscevB4n3o85faXPG7D</el-copyable>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <code class="block bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded px-3 py-3 text-sm md:text-base font-mono text-gray-800 dark:text-gray-200 break-words text-center sm:text-left">PbvihBLgz6cFJnhYscevB4n3o85faXPG7D</code>
                    </div>
                    <button type="button" command="--copy" commandfor="donation-address" class="shrink-0 group relative inline-flex min-w-40 cursor-pointer items-center justify-center px-6 py-3 bg-green-700 hover:bg-green-800 text-white rounded-lg transition-colors text-sm font-bold uppercase tracking-wide">
                        <span class="group-data-[copied]:hidden">Copy Address</span>
                        <span class="hidden group-data-[copied]:inline">Copied!</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- DigitalOcean Referral -->
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <div class="hidden md:flex shrink-0 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                    <x-icon-digitalocean class="w-12 h-12 text-[#0080FF]" />
                </div>
                <div class="flex-1 space-y-4">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">Host with DigitalOcean</h2>
                    <p class="text-green-600 dark:text-green-400 font-bold mb-2">Get $200 in free credit</p>
                    <div class="space-y-4 text-gray-600 dark:text-gray-400 leading-relaxed">
                        <p>New users receive $200 in credit to explore DigitalOcean's cloud platform.</p>
                        <p>By using our link, you support peppool.space at no extra cost.</p>
                        <p>Once you've spent $25, the project receives $25 in credit to help cover our infrastructure costs.</p>
                    </div>
                    <a href="{{ $digitalOceanUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg px-6 py-3 bg-green-700 hover:bg-green-800 text-white text-sm font-bold uppercase tracking-wide shadow-xs transition-colors">
                        <span>Claim your $200 credit</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Fathom Analytics -->
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <div class="hidden md:flex shrink-0 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <x-icon-fathom class="w-12 h-12 text-gray-600 dark:text-gray-400" />
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">Start Fathom analytics free trial</h2>
                    <p class="text-green-600 dark:text-green-400 font-bold mb-2">A Google Analytics alternative</p>
                    <p class="text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
                        Privacy-focused, cookie-free analytics that comply with GDPR, CCPA, and more.<br>
                        Simple to set up, easy to use, and bot-free data.
                    </p>
                    <p class="text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
                        Trusted by IBM, GitHub, Laravel and over a million websites.
                    </p>
                    <a href="{{ $fathomUrl }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg px-6 py-3 bg-green-700 hover:bg-green-800 text-white text-sm font-bold uppercase tracking-wide shadow-xs transition-colors">
                        <span>Free trial with $10 credit</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- External Sponsors -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="https://github.com/sponsors/mvdnbrk" target="_blank" rel="noopener" class="flex items-center gap-4 p-6 bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-500 transition-colors group">
                <div class="p-3 bg-white dark:bg-gray-800 rounded-full text-gray-900 dark:text-gray-100 shadow-sm">
                    <x-icon-github class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-green-600 transition-colors">GitHub Sponsors</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Monthly or one-time sponsorship via GitHub.</p>
                </div>
            </a>

            <a href="https://thanks.dev/u/gh/mvdnbrk" target="_blank" rel="noopener" class="flex items-center gap-4 p-6 bg-white dark:bg-gray-900 shadow rounded-lg border border-gray-200 dark:border-gray-700 hover:border-black dark:hover:border-white transition-colors group">
                <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-gray-900 to-black rounded-full text-white font-bold text-lg font-mono shrink-0 shadow-sm">
                    <span class="bg-white text-black rounded px-1.5 py-0.5 text-xs font-bold">{}</span>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-black dark:group-hover:text-white transition-colors">thanks.dev</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Support open-source contributors directly.</p>
                </div>
            </a>
        </div>
    </div>
</x-layout>
