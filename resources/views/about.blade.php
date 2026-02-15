<x-layout title="About Pepecoin - peppool.space" og_image="about-pepecoin.png" og_description="About the Pepecoin (PEP) network and community.">
    <div class="mb-6 md:mb-8 text-gray-600 dark:text-gray-300">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-200 mb-2">About Pepecoin</h1>
        <p class="text-sm md:text-base text-gray-500">Overview of the Pepecoin (PEP) network, technology and community.</p>
    </div>

    <div class="max-w-none space-y-8">
        <!-- About Pepecoin -->
        <div class="bg-white dark:bg-gray-900 shadow-2xs rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100">What is Pepecoin?</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
                Pepecoin Network is the world's first fully decentralized and secure blockchain dedicated to the iconic Pepe the Frog meme. Launched as a community-driven cryptocurrency, Pepecoin (PEP) aims to recapture the fun and camaraderie of the early Dogecoin community, created by one of its original supporters from 2013.
            </p>
            <p class="text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
                Built as a layer-1 blockchain, Pepecoin uses the Scrypt algorithm and supports merged mining with Litecoin and Dogecoin, ensuring robust security through their combined hashrate. Pepecoin is designed for fast, low-cost transactions, with confirmations taking just minutes and fees often less than a penny.
            </p>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                It operates on a Proof-of-Work consensus model, maintaining decentralization with no premine, preallocation, or ICO, ensuring a fair launch for all. The network issues fixed block rewards, halving every 100,000 blocks, with a permanent reward of 10,000 PEP per block starting at block 600,000.
            </p>
        </div>

        <!-- Technology & Community -->
        <div class="bg-white dark:bg-gray-900 shadow-2xs rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100">Technology & Community</h2>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                The Pepecoin Core software, forked from Bitcoin and Dogecoin Core, is open-source and community-developed, inviting contributions from developers and enthusiasts alike. With a vibrant community at its heart, Pepecoin fosters creativity and connection through memes, discussions, and shared projects.
            </p>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                Join us to explore the Pepecoin ecosystem, run a node, mine PEP, or connect with fellow "frens" in this fun, decentralized journey!
            </p>
        </div>

        <!-- About the Creator -->
        <div class="bg-white dark:bg-gray-900 shadow-2xs rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100">About the Creator</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
                Peppool.space was created by Mark (<a href="https://x.com/mvdnbrk" target="_blank" rel="noopener" class="text-green-700 hover:text-green-800 dark:text-green-600 dark:hover:text-green-400 underline">@mvdnbrk on X</a>), a seasoned developer with 30 years of experience, dedicated to fostering the Pepecoin Network's decentralized, community-driven vision through an efficient and trusted Pepecoin blockchain explorer.
            </p>
            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                Follow <a href="https://x.com/mvdnbrk" target="_blank" rel="noopener" class="text-green-700 hover:text-green-800 dark:text-green-600 dark:hover:text-green-400 underline">Mark on X</a> for updates and insights on Pepecoin and Peppool.space, or check out his work on <a href="https://github.com/mvdnbrk" target="_blank" rel="noopener" class="text-green-700 hover:text-green-800 dark:text-green-600 dark:hover:text-green-400 underline">GitHub</a>!
            </p>
            <div class="mt-6">
                <a href="{{ route('sponsor') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg transition-colors font-medium">
                    <span>Sponsor the project</span>
                    <x-icon-arrow-right class="w-4 h-4" />
                </a>
            </div>
        </div>

        <!-- Community Links -->
        <div class="bg-white dark:bg-gray-900 shadow-2xs rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100">Connect with the Pepecoin Community</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="{{ $socials->twitter_url }}" target="_blank" rel="noopener" class="flex items-center gap-4 p-6 bg-white dark:bg-gray-900 shadow-2xs rounded-lg border border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-500 transition-colors group">
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full text-green-600">
                    <x-icon-x class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-green-600 transition-colors">Follow on X</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Stay updated with the latest news and announcements.</p>
                </div>
            </a>

            <a href="{{ $socials->telegram_url }}" target="_blank" rel="noopener" class="flex items-center gap-4 p-6 bg-white dark:bg-gray-900 shadow-2xs rounded-lg border border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-500 transition-colors group">
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full text-green-600">
                    <x-icon-telegram class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-green-600 transition-colors">Join Telegram</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Chat with the community and get real-time updates.</p>
                </div>
            </a>

            <a href="{{ $socials->discord_url }}" target="_blank" rel="noopener" class="flex items-center gap-4 p-6 bg-white dark:bg-gray-900 shadow-2xs rounded-lg border border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-500 transition-colors group">
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full text-green-600">
                    <x-icon-discord class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-green-600 transition-colors">Join Discord</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Connect with fellow Pepecoin enthusiasts.</p>
                </div>
            </a>

            <a href="{{ $socials->reddit_url }}" target="_blank" rel="noopener" class="flex items-center gap-4 p-6 bg-white dark:bg-gray-900 shadow-2xs rounded-lg border border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-500 transition-colors group">
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full text-green-600">
                    <x-icon-reddit class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-green-600 transition-colors">Join on Reddit</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Discuss Pepecoin in our subreddit community.</p>
                </div>
            </a>

            <a href="{{ $socials->facebook_url }}" target="_blank" rel="noopener" class="flex items-center gap-4 p-6 bg-white dark:bg-gray-900 shadow-2xs rounded-lg border border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-500 transition-colors group">
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full text-green-600">
                    <x-icon-facebook class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-green-600 transition-colors">Follow on Facebook</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Join our Facebook community for updates.</p>
                </div>
            </a>

            <a href="{{ $socials->tiktok_url }}" target="_blank" rel="noopener" class="flex items-center gap-4 p-6 bg-white dark:bg-gray-900 shadow-2xs rounded-lg border border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-500 transition-colors group">
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full text-green-600">
                    <x-icon-tiktok class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-green-600 transition-colors">Follow on TikTok</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Watch fun Pepecoin content and memes.</p>
                </div>
            </a>

            <a href="{{ $socials->instagram_url }}" target="_blank" rel="noopener" class="flex items-center gap-4 p-6 bg-white dark:bg-gray-900 shadow-2xs rounded-lg border border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-500 transition-colors group">
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full text-green-600">
                    <x-icon-instagram class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-green-600 transition-colors">Follow on Instagram</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">See the latest Pepecoin visuals and updates.</p>
                </div>
            </a>

            <a href="{{ $socials->youtube_url }}" target="_blank" rel="noopener" class="flex items-center gap-4 p-6 bg-white dark:bg-gray-900 shadow-2xs rounded-lg border border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-500 transition-colors group">
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full text-green-600">
                    <x-icon-youtube class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-green-600 transition-colors">Watch on YouTube</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Educational content and Pepecoin tutorials.</p>
                </div>
            </a>

            <a href="{{ $socials->github_url }}" target="_blank" rel="noopener" class="flex items-center gap-4 p-6 bg-white dark:bg-gray-900 shadow-2xs rounded-lg border border-gray-200 dark:border-gray-700 hover:border-green-500 dark:hover:border-green-500 transition-colors group">
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-full text-green-600">
                    <x-icon-github class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-green-600 transition-colors">View on GitHub</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Explore the open-source Pepecoin repositories.</p>
                </div>
            </a>
        </div>
        </div>
</x-layout>
