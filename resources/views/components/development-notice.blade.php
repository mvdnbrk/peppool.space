<div 
    x-data="{
        isOpen: false,
        showComponent: false,
        init() {
            this.isOpen = localStorage.getItem('devNoticeMinimized') !== 'true';
            setTimeout(() => {
                this.showComponent = true;
            }, 2000);
        },
        toggle() {
            this.isOpen = !this.isOpen;
            localStorage.setItem('devNoticeMinimized', !this.isOpen);
        }
    }"
    x-init="init()"
>
    <!-- Development Notice Component -->
    <div 
        x-show="showComponent"
        x-transition:enter="transition ease-out duration-1000"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="fixed bottom-0 left-4 z-50"
    >
        <!-- Speech Balloon Container -->
        <div class="relative">
            <!-- Main Content Box -->
            <div 
                x-show="isOpen"
                x-transition:enter="transition ease-out duration-1000"
                x-transition:enter-start="opacity-0 transform scale-90 translate-y-4"
                x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 transform scale-90 translate-y-4"
                class="bg-green-100 dark:bg-green-800 border border-green-200 dark:border-green-600 text-green-800 dark:text-green-100 rounded-lg shadow-lg p-4 mb-4 ml-4 max-w-xs lg:max-w-sm relative"
                role="alert"
            >
                <!-- Speech balloon tail -->
                <div class="absolute -bottom-2 left-8 w-4 h-4 bg-green-100 dark:bg-green-800 border-r border-b border-green-200 dark:border-green-600 transform rotate-45"></div>
                
                <!-- Header with Close Button -->
                <div class="flex items-start justify-between mb-2">
                    <h3 class="font-bold text-green-800 dark:text-green-200 text-sm">
                        Development in Progress
                    </h3>
                    <button
                        @click="toggle()"
                        class="ml-2 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 transition-colors p-0.5 cursor-pointer"
                        aria-label="Minimize development notice"
                    >
                        <x-icon-minus class="w-4 h-4" />
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

            <!-- Pepe Container - Always present at bottom -->
            <div 
                @click="toggle()"
                class="cursor-pointer hover:opacity-80 transition-all duration-500 flex justify-start w-12 h-12 sm:w-14 sm:h-14"
                :class="isOpen ? 'w-20 h-20 sm:w-24 sm:h-24' : 'w-12 h-12 sm:w-14 sm:h-14'"
                role="button" 
                :aria-label="isOpen ? 'Minimize development notice' : 'Open development notice'"
            >
                <x-custom-pizza-ninjas-pepe class="w-full h-full object-contain"/>
            </div>
        </div>
    </div>
</div>
