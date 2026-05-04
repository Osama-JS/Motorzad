<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('لوحة تحكم المزايد') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-4">مرحباً بك في لوحة تحكم المزايد</h1>
                    <p>أهلاً بك، {{ auth()->user()->full_name }}. هنا يمكنك متابعة نشاطك في المزادات.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                        <div class="bg-blue-100 dark:bg-blue-900 p-6 rounded-lg shadow-sm border border-blue-200 dark:border-blue-800">
                            <h3 class="font-semibold text-blue-800 dark:text-blue-200 text-lg">مزايداتي النشطة</h3>
                            <p class="text-3xl font-bold mt-2 text-blue-600 dark:text-blue-400">0</p>
                        </div>
                        <div class="bg-green-100 dark:bg-green-900 p-6 rounded-lg shadow-sm border border-green-200 dark:border-green-800">
                            <h3 class="font-semibold text-green-800 dark:text-green-200 text-lg">مزادات ربحتها</h3>
                            <p class="text-3xl font-bold mt-2 text-green-600 dark:text-green-400">0</p>
                        </div>
                        <div class="bg-purple-100 dark:bg-purple-900 p-6 rounded-lg shadow-sm border border-purple-200 dark:border-purple-800">
                            <h3 class="font-semibold text-purple-800 dark:text-purple-200 text-lg">قائمة الأمنيات</h3>
                            <p class="text-3xl font-bold mt-2 text-purple-600 dark:text-purple-400">0</p>
                        </div>
                    </div>

                    <div class="mt-10">
                        <h2 class="text-xl font-bold mb-4">آخر النشاطات</h2>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg text-center text-gray-500">
                            لا توجد نشاطات حديثة حتى الآن.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
