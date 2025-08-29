<!-- Development Notice Component -->
<div id="dev-notice" class="fixed bottom-4 left-4 right-4 sm:right-auto sm:max-w-sm lg:max-w-md z-50 bg-green-100 dark:bg-green-800 border border-green-200 dark:border-green-600 text-green-800 dark:text-green-100 p-4 rounded-lg shadow-lg transition-all duration-300" role="alert" style="display: none;">
    <div class="flex items-start gap-3 sm:gap-4">
        <!-- Pepe Avatar -->
        <div class="flex-shrink-0 self-end">
            <button
                id="pepe-avatar"
                class="w-20 h-20 sm:w-24 sm:h-24 bg-green-100 dark:bg-green-700 rounded-lg p-2 sm:p-2.5 flex items-center justify-center hover:bg-green-200 dark:hover:bg-green-600 transition-colors cursor-pointer"
                aria-label="Minimize development notice"
            >
                <x-views.components.svg.pizza-ninjas-pepe class="w-full h-full" />
            </button>
        </div>

        <!-- Main Content -->
        <div id="notice-content" class="flex-1 min-w-0">
            <!-- Header with Close Button -->
            <div class="flex items-start justify-between mb-2">
                <h3 class="font-bold text-green-800 dark:text-green-200 text-sm">
                    Development in Progress
                </h3>
                <button
                    id="minimize-dev-notice"
                    class="ml-2 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 transition-colors p-0.5 cursor-pointer hover:cursor-pointer"
                    aria-label="Minimize development notice"
                >
                    <x-icon-close class="w-4 h-4" />
                </button>
            </div>

            <!-- Message Content -->
            <div class="text-xs leading-relaxed text-green-700 dark:text-green-300 space-y-1 mb-2">
                <p>We're swimming through the Pepe Mempool,<br>adding new features and improvements.</p>
                <p>Some things might be a bit splashy as we refine the <span class="whitespace-nowrap">platform.</span></p>
            </div>

            <!-- Bottom Row: Thanks message left, X logo right -->
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-green-700 dark:text-green-300">Thanks for your patience!</p>
                <a
                    href="https://x.com/mvdnbrk"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 transition-colors p-0.5"
                    aria-label="Follow @mvdnbrk on X"
                >
                    <x-icon-x class="w-4 h-4" />
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Minimized Pepe Notice -->
<div id="pepe-minimized" class="fixed bottom-4 left-4 z-50 bg-green-100 dark:bg-green-700 rounded-lg p-2 shadow-lg cursor-pointer hover:bg-green-200 dark:hover:bg-green-600 transition-all duration-300" style="display: none;" role="button" aria-label="Open development notice">
    <div class="w-16 h-16 sm:w-20 sm:h-20 flex items-center justify-center">
        <x-views.components.svg.pizza-ninjas-pepe class="w-full h-full" />
    </div>
</div>
