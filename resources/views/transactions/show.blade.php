<x-app-layout>
    @php
        $rupiah = fn($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
        $itemSubtotal = fn($it) => (float) ($it->quantity ?? 0) * (float) ($it->price ?? 0);
        $itemsTotal = $tx->items->sum(fn($it) => $itemSubtotal($it));
        $diff = (float) $tx->amount - $itemsTotal;
        $gemini = is_array($tx->gemini_data) ? $tx->gemini_data : (json_decode($tx->gemini_data ?? '[]', true) ?: []);
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100">Detail Transaksi</h2>

            <div class="flex items-center gap-2">
                <x-secondary-link href="{{ route('transactions.index') }}">
                    ← Kembali
                </x-secondary-link>
            </div>
        </div>
    </x-slot>

    <div>
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-2 md:gap-6">
            {{-- Gambar Struk --}}
            <div class="lg:col-span-4">
                <div class="rounded-xl bg-white dark:bg-gray-800 p-3 ring-1 ring-gray-200 dark:ring-gray-700 transition-colors duration-200">
                    <div class="text-gray-700 dark:text-gray-300 text-sm mb-2">Struk</div>
                    @if ($tx->image)
                        <img src="{{ asset('storage/' . $tx->image) }}" alt="Receipt"
                            class="rounded-lg w-full object-contain max-h-[420px]">
                        <div class="mt-3">
                            <a href="{{ asset('storage/' . $tx->image) }}" target="_blank"
                                class="inline-flex items-center gap-2 text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 text-sm transition-colors duration-200">
                                Lihat ukuran penuh
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M13.5 6H18m0 0v4.5M18 6l-9 9" />
                                </svg>
                            </a>
                        </div>
                    @else
                        <div class="text-gray-500 dark:text-gray-400 text-sm">Tidak ada gambar struk.</div>
                    @endif
                </div>
            </div>

            {{-- Kartu ringkas --}}
            <div class="lg:col-span-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-2 md:gap-4 mb-4">
                    <div class="rounded-xl bg-white dark:bg-gray-800 p-4 ring-1 ring-gray-200 dark:ring-gray-700 transition-colors duration-200">
                        <div class="text-gray-600 dark:text-gray-400 text-sm">Tanggal</div>
                        <div class="text-gray-900 dark:text-gray-100 font-semibold">
                            {{ \Illuminate\Support\Carbon::parse($tx->date)->isoFormat('DD MMMM YYYY') }}</div>
                    </div>
                    <div class="rounded-xl bg-white dark:bg-gray-800 p-4 ring-1 ring-gray-200 dark:ring-gray-700 transition-colors duration-200">
                        <div class="text-gray-600 dark:text-gray-400 text-sm">Tipe</div>
                        <div>
                            @if ($tx->type === 'income')
                                <span
                                    class="inline-flex items-center rounded-lg bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 px-2 py-1 text-xs font-medium">Income</span>
                            @else
                                <span
                                    class="inline-flex items-center rounded-lg bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 px-2 py-1 text-xs font-medium">Expense</span>
                            @endif
                        </div>
                    </div>
                    <div class="rounded-xl bg-white dark:bg-gray-800 p-4 ring-1 ring-gray-200 dark:ring-gray-700 transition-colors duration-200">
                        <div class="text-gray-600 dark:text-gray-400 text-sm">Kategori</div>
                        <div class="text-gray-900 dark:text-gray-100 font-semibold">{{ $tx->category->name ?? '-' }}</div>
                    </div>
                    <div class="rounded-xl bg-white dark:bg-gray-800 p-4 ring-1 ring-gray-200 dark:ring-gray-700 transition-colors duration-200">
                        <div class="text-gray-600 dark:text-gray-400 text-sm">Nominal</div>
                        <div class="text-gray-900 dark:text-gray-100 font-semibold">{{ $rupiah($tx->amount) }}</div>
                    </div>
                </div>

                <div class="rounded-xl bg-white dark:bg-gray-800 p-6 ring-1 ring-gray-200 dark:ring-gray-700 transition-colors duration-200 mb-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Judul</div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $tx->title }}</h3>
                        </div>
                    </div>

                    @if ($tx->note)
                        <div class="mt-3 text-gray-700 dark:text-gray-300">
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Catatan</div>
                            <p class="whitespace-pre-line">{{ $tx->note }}</p>
                        </div>
                    @endif
                </div>

                {{-- Items (jika ada) --}}
                <div class="rounded-xl bg-white dark:bg-gray-800 p-6 ring-1 ring-gray-200 dark:ring-gray-700 transition-colors duration-200 mb-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">Item</h4>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Subtotal Item: <span class="font-medium text-gray-900 dark:text-gray-100">{{ $rupiah($itemsTotal) }}</span>
                        </div>
                    </div>

                    @if ($tx->items->count())
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                    <tr>
                                        <th class="text-left px-3 py-2 font-medium">#</th>
                                        <th class="text-left px-3 py-2 font-medium">Nama</th>
                                        <th class="text-right px-3 py-2 font-medium">Qty</th>
                                        <th class="text-right px-3 py-2 font-medium">Harga</th>
                                        <th class="text-right px-3 py-2 font-medium">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-600">
                                    @foreach ($tx->items as $i => $it)
                                        <tr>
                                            <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $i + 1 }}</td>
                                            <td class="px-3 py-2 text-gray-900 dark:text-gray-100">{{ $it->name }}</td>
                                            <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-300">
                                                {{ number_format((float) $it->quantity, 0, ',', '.') }}</td>
                                            <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-300">{{ $rupiah($it->price) }}
                                            </td>
                                            <td class="px-3 py-2 text-right text-gray-900 dark:text-gray-100 font-medium">
                                                {{ $rupiah($itemSubtotal($it)) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="4" class="px-3 py-2 text-right font-medium text-gray-700">
                                            Subtotal Item</td>
                                        <td class="px-3 py-2 text-right font-semibold text-gray-900">
                                            {{ $rupiah($itemsTotal) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="px-3 py-2 text-right font-medium text-gray-700">
                                            Nominal Transaksi</td>
                                        <td class="px-3 py-2 text-right font-semibold text-gray-900">
                                            {{ $rupiah($tx->amount) }}</td>
                                    </tr>
                                    @if (abs($diff) >= 1)
                                        <tr>
                                            <td colspan="4" class="px-3 py-2 text-right font-medium text-gray-700">
                                                Selisih</td>
                                            <td
                                                class="px-3 py-2 text-right font-semibold {{ $diff === 0 ? 'text-gray-900' : ($diff > 0 ? 'text-blue-700' : 'text-red-700') }}">
                                                {{ $rupiah($diff) }}
                                            </td>
                                        </tr>
                                    @endif
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">Tidak ada item.</p>
                    @endif
                </div>

                {{-- Raw Gemini data (opsional) --}}
                <div class="rounded-xl bg-white dark:bg-gray-800 p-6 ring-1 ring-gray-200 dark:ring-gray-700 transition-colors duration-200">
                    <details>
                        <summary class="cursor-pointer text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">Lihat data Gemini
                            (raw)</summary>
                        <pre class="mt-3 text-xs bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 p-3 rounded-lg ring-1 ring-gray-200 dark:ring-gray-600 overflow-auto">{{ json_encode($gemini, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </details>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
