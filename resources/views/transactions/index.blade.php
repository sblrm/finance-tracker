<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100">Transaksi</h2>
            <x-primary-link href="{{ route('transactions.create') }}">
                <i class="bx bx-plus"></i> Tambah
            </x-primary-link>
        </div>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 px-4 py-3 ring-1 ring-blue-200 dark:ring-blue-800 mb-3">
                    {{ session('success') }}
                </div>
            @endif

            @foreach ($transactions as $item)
                <a href="{{ route('transactions.show', $item) }}"
                    class="block bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 mb-1 rounded-xl p-6 transition-all duration-200 hover:border-blue-500 dark:hover:border-blue-400 hover:shadow-lg">
                    <div class="sm:flex items-center gap-2 sm:gap-6">
                        <div>
                            <h3 class="text-base md:text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ $item->title }}</h3>
                            <span class="text-xs md:text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->date->translatedFormat('d F Y') }} &middot;
                                {{ $item->category->name ?? 'Tanpa Kategori' }}
                            </span>
                        </div>
                        <div class="sm:ms-auto mt-2 sm:mt-0">
                            <span
                                class="sm:ms-auto w-max block px-3 py-1 rounded-xl font-semibold text-xs sm:text-sm {{ $item->type == 'income' ? 'text-blue-700 dark:text-blue-300 bg-blue-200 dark:bg-blue-900/30' : 'text-red-700 dark:text-red-300 bg-red-200 dark:bg-red-900/30' }}">
                                {{ $item->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                            </span>
                            <p class="text-base sm:text-xl font-bold mt-1 text-gray-900 dark:text-gray-100 sm:text-end">
                                Rp {{ number_format($item->amount, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach

            <div>
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
