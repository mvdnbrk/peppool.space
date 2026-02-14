<x-layout title="About Pepecoin - peppool.space" og_image="about-pepecoin.png" og_description="About the Pepecoin (PEP) network and community.">
    <div class="mb-6 md:mb-8 text-gray-600 dark:text-gray-300">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-200 mb-2">About Pepecoin</h1>
        <p class="text-sm md:text-base text-gray-500">Overview of the Pepecoin (PEP) network, technology and community.</p>
    </div>

    <div class="max-w-none space-y-4 md:space-y-6">
        <p class="text-base md:text-lg leading-7 md:leading-8 text-gray-600 dark:text-gray-300">
            Pepecoin Network is the world's first fully decentralized and secure blockchain dedicated to the iconic Pepe the Frog meme. Launched as a community-driven cryptocurrency, Pepecoin (PEP) aims to recapture the fun and camaraderie of the early Dogecoin community, created by one of its original supporters from 2013.
        </p>
        <p class="text-base md:text-lg leading-7 md:leading-8 text-gray-600 dark:text-gray-300">
            Built as a layer-1 blockchain, Pepecoin uses the Scrypt algorithm and supports merged mining with Litecoin and Dogecoin, ensuring robust security through their combined hashrate. Pepecoin is designed for fast, low-cost transactions, with confirmations taking just minutes and fees often less than a penny.
        </p>
        <p class="text-base md:text-lg leading-7 md:leading-8 text-gray-600 dark:text-gray-300">
            It operates on a Proof-of-Work consensus model, maintaining decentralization with no premine, preallocation, or ICO, ensuring a fair launch for all. The network issues fixed block rewards, halving every 100,000 blocks, with a permanent reward of 10,000 PEP per block starting at block 600,000.
        </p>
        <p class="text-base md:text-lg leading-7 md:leading-8 text-gray-600 dark:text-gray-300">
            The Pepecoin Core software, forked from Bitcoin and Dogecoin Core, is open-source and community-developed, inviting contributions from developers and enthusiasts alike. With a vibrant community at its heart, Pepecoin fosters creativity and connection through memes, discussions, and shared projects.
        </p>
        <p class="text-base md:text-lg leading-7 md:leading-8 text-gray-600 dark:text-gray-300">
            Join us to explore the Pepecoin ecosystem, run a node, mine PEP, or connect with fellow "frens" in this fun, decentralized journey!
        </p>
    </div>

    <!-- About the Creator Section -->
    <div class="mt-8 md:mt-12 pt-6 md:pt-8 border-t border-gray-200 dark:border-gray-700">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-200 mb-4">
            About the creator of peppool.space
        </h2>
        <p class="text-base md:text-lg leading-7 md:leading-8 text-gray-600 dark:text-gray-300 mb-4">
            Peppool.space was created by Mark (<a href="https://x.com/mvdnbrk" target="_blank" rel="noopener" class="text-green-700 hover:text-green-800 dark:text-green-600 dark:hover:text-green-400 underline">@mvdnbrk on X</a>), a seasoned developer with 30 years of experience, dedicated to fostering the Pepecoin Network's decentralized, community-driven vision through an efficient and trusted Pepecoin blockchain explorer.
        </p>
        <p class="text-base md:text-lg leading-7 md:leading-8 text-gray-600 dark:text-gray-300">
            Follow <a href="https://x.com/mvdnbrk" target="_blank" rel="noopener" class="text-green-700 hover:text-green-800 dark:text-green-600 dark:hover:text-green-400 underline">Mark on X</a> for updates and insights on Pepecoin and Peppool.space, or check out his work on <a href="https://github.com/mvdnbrk" target="_blank" rel="noopener" class="text-green-700 hover:text-green-800 dark:text-green-600 dark:hover:text-green-400 underline">GitHub</a>!
        </p>
    </div>

    <!-- Social Media Section -->
    <div class="mt-8 md:mt-12 pt-6 md:pt-8 border-t border-gray-200 dark:border-gray-700">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-200 mb-4">Connect with the Community</h2>
        <div class="flex flex-col sm:flex-row flex-wrap gap-4">
            <a href="{{ $socials->twitter_url }}" target="_blank" rel="noopener" aria-label="Follow Pepecoin on X (Twitter)" class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg transition-colors">
                <x-icon-x class="w-4 h-4" />
                <span class="font-medium">Follow on X</span>
            </a>
            <a href="{{ $socials->telegram_url }}" target="_blank" rel="noopener" aria-label="Join Pepecoin Telegram group" class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg transition-colors">
                <x-icon-telegram class="w-4 h-4" />
                <span class="font-medium">Join Telegram</span>
            </a>
            <a href="{{ $socials->discord_url }}" target="_blank" rel="noopener" aria-label="Join Pepecoin Discord community" class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg transition-colors">
                <x-icon-discord class="w-4 h-4" />
                <span class="font-medium">Join Discord</span>
            </a>
            <a href="{{ $socials->reddit_url }}" target="_blank" rel="noopener" aria-label="Join Pepecoin community on Reddit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg transition-colors">
                <x-icon-reddit class="w-4 h-4" />
                <span class="font-medium">Join on Reddit</span>
            </a>
            <a href="{{ $socials->facebook_url }}" target="_blank" rel="noopener" aria-label="Follow Pepecoin on Facebook" class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg transition-colors">
                <x-icon-facebook class="w-4 h-4" />
                <span class="font-medium">Follow on Facebook</span>
            </a>
            <a href="{{ $socials->tiktok_url }}" target="_blank" rel="noopener" aria-label="Follow Pepecoin on TikTok" class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg transition-colors">
                <x-icon-tiktok class="w-4 h-4" />
                <span class="font-medium">Follow on TikTok</span>
            </a>
            <a href="{{ $socials->instagram_url }}" target="_blank" rel="noopener" aria-label="Follow Pepecoin on Instagram" class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg transition-colors">
                <x-icon-instagram class="w-4 h-4" />
                <span class="font-medium">Follow on Instagram</span>
            </a>
            <a href="{{ $socials->youtube_url }}" target="_blank" rel="noopener" aria-label="Watch Pepecoin videos on YouTube" class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg transition-colors">
                <x-icon-youtube class="w-4 h-4" />
                <span class="font-medium">Watch on YouTube</span>
            </a>
            <a href="{{ $socials->github_url }}" target="_blank" rel="noopener" aria-label="View Pepecoin source code on GitHub" class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg transition-colors">
                <x-icon-github class="w-4 h-4" />
                <span class="font-medium">View on GitHub</span>
            </a>
        </div>
    </div>

    <!-- Support Section -->
    <div class="mt-8 md:mt-12 pt-6 md:pt-8 border-t border-gray-200 dark:border-gray-700">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-gray-200 mb-4">Support the Project</h2>
        <p class="text-base md:text-lg leading-7 md:leading-8 text-gray-600 dark:text-gray-300 mb-4">
            If you find <span class="text-green-800 dark:text-green-600">peppool.space</span> useful and would like to support its development and maintenance, you can make a donation using Pepecoin.
        </p>
        <div class="bg-gray-300 dark:bg-gray-800 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200 mb-3">
                Donation Address
            </h3>
            <el-copyable id="donation-address" class="hidden">PbvihBLgz6cFJnhYscevB4n3o85faXPG7D</el-copyable>
            <div class="flex flex-row flex-nowrap items-center gap-3 w-full">
                <code class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded px-3 py-2 text-sm font-mono text-gray-800 dark:text-gray-200 whitespace-nowrap overflow-x-auto flex-1 min-w-0">
                    PbvihBLgz6cFJnhYscevB4n3o85faXPG7D
                </code>
                <button type="button" command="--copy" commandfor="donation-address" class="group relative inline-flex shrink-0 cursor-pointer items-center px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg transition-colors text-sm font-medium whitespace-nowrap">
                    <span class="group-data-[copied]:hidden">Copy Address</span>
                    <span class="hidden group-data-[copied]:inline">Copied!</span>
                </button>
            </div>
            <p class="text-base text-gray-500 dark:text-gray-400 mt-3">
                Your support helps keep this blockchain explorer running and continuously improving. Thank you!
            </p>
        </div>
    </div>
</x-layout>
