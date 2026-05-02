<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Resources') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-8 mb-8 text-white shadow-xl overflow-hidden relative">
                <div class="relative z-10">
                    <h1 class="text-4xl font-bold mb-4">Educational Resources</h1>
                    <p class="text-xl text-blue-100 max-w-2xl">Everything you need to succeed with Motorzad. From trading guides to technical analysis, we've got you covered.</p>
                </div>
                <!-- Decorative background element -->
                <div class="absolute top-0 right-0 -mt-20 -mr-20 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-blue-400 opacity-20 rounded-full blur-3xl"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Trading Guides -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Trading Guides</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Comprehensive guides for beginners and advanced traders alike.</p>
                        <a href="#" class="inline-flex items-center text-blue-600 dark:text-blue-400 font-medium hover:underline">
                            Explore Guides
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>

                <!-- Video Tutorials -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Video Tutorials</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Watch our expert traders break down strategies in step-by-step videos.</p>
                        <a href="#" class="inline-flex items-center text-purple-600 dark:text-purple-400 font-medium hover:underline">
                            Watch Videos
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>

                <!-- Market Analysis -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Market Analysis</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Daily insights and deep dives into the current market trends.</p>
                        <a href="#" class="inline-flex items-center text-green-600 dark:text-green-400 font-medium hover:underline">
                            Read Analysis
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- FAQ or Additional Section -->
            <div class="mt-12 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">Frequently Asked Questions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-2">How do I start trading?</h4>
                        <p class="text-gray-600 dark:text-gray-400">Create an account, verify your identity, and follow our "Getting Started" guide in the Trading Guides section.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-2">Are the signals guaranteed?</h4>
                        <p class="text-gray-600 dark:text-gray-400">While our signals have a high accuracy rate, all trading involves risk. We recommend using proper risk management.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-2">Can I download the resources?</h4>
                        <p class="text-gray-600 dark:text-gray-400">Yes, most of our PDF guides are available for offline viewing once you are logged in.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white mb-2">Do you offer 1-on-1 coaching?</h4>
                        <p class="text-gray-600 dark:text-gray-400">We offer premium coaching packages for our VIP members. Contact support for more details.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
