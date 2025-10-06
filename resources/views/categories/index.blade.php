<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Kategori Transaksi') }}
            </h2>
            <div>
                <x-primary-link href="{{ route('categories.create') }}">
                    <i class='bx bx-plus'></i> Tambah
                </x-primary-link>
            </div>
        </div>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 px-4 py-3 ring-1 ring-blue-200 dark:ring-blue-800 mb-3">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 px-4 py-3 ring-1 ring-red-200 dark:ring-red-800 mb-3">
                    {{ session('error') }}
                </div>
            @endif

            @foreach ($categories as $item)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mb-3 transition-colors duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ $item->name }}</h3>
                            <span class="text-sm {{ $item->type === 'income' ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }}">
                                Tipe: {{ ucfirst($item->type) }}
                            </span>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('categories.edit', $item->id) }}"
                                class="text-xl text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition ease-in-out duration-150">
                                <i class="bx bx-edit"></i>
                            </a>
                            <form action="{{ route('categories.destroy', $item->id) }}" method="post"
                                onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-xl text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition ease-in-out duration-150">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
