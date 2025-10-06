<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 md:gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 transition-colors duration-200">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Total Income</h3>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">Rp
                        {{ number_format(Auth::user()->transactions->where('type', 'income')->sum('amount')) }}
                    </p>
                </div>
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 transition-colors duration-200">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Total Expense</h3>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">Rp
                        {{ number_format(Auth::user()->transactions->where('type', 'expense')->sum('amount')) }}
                    </p>
                </div>
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 transition-colors duration-200">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Net Balance</h3>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">Rp
                        {{ number_format(Auth::user()->transactions->where('type', 'income')->sum('amount') - Auth::user()->transactions->where('type', 'expense')->sum('amount')) }}
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 md:gap-6">
                <a href="{{ route('categories.index') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 transition-all duration-200 hover:shadow-lg hover:border-blue-500 dark:hover:border-blue-400 group">
                    <div class="flex items-center justify-center">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="block mx-auto size-9 text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                            </svg>
                            <p class="text-center text-base sm:text-2xl font-medium mb-0 mt-2 text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">Manage Kategori</p>
                        </div>
                    </div>
                </a>
                <a href="{{ route('transactions.create') }}"
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 transition-all duration-200 hover:shadow-lg hover:border-blue-500 dark:hover:border-blue-400 group">
                    <div class="flex items-center justify-center">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="block mx-auto size-9 text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>

                            <p class="text-center text-base sm:text-2xl font-medium mb-0 mt-2 text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200">Buat Transaksi</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
</x-app-layout>
