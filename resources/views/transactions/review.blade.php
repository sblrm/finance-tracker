<x-app-layout>
    <!-- Preload gambar struk dengan cache hints -->
    @push('head')
        <link rel="preload" as="image" href="{{ asset('storage/' . $prefill['receipt_path']) }}" crossorigin="anonymous">
        <meta name="asset-cache" content="max-age=3600">
    @endpush

    <div x-data="reviewExtracted({{ json_encode($prefill) }})">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Review Hasil Ekstraksi Struk</h2>
        </x-slot>

        <div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Preview Struk -->
                    <div class="lg:col-span-5" :class="{ 'hidden lg:block': !showPreview }" x-show="showPreview || $screen('lg')">
                        <div class="sticky top-6">
                            <div class="rounded-xl bg-white dark:bg-gray-800 p-4 ring-1 ring-gray-200 dark:ring-gray-700 transition-colors duration-200">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-600 dark:text-blue-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                    Preview Struk
                                </h3>
                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-2 relative">
                                    <!-- Simple loading indicator -->
                                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-800 rounded-lg" id="imageLoader">
                                        <div class="loading-spinner mb-3"></div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Memuat gambar...</p>
                                    </div>
                                    
                                    <img src="{{ asset('storage/' . $prefill['receipt_path']) }}"
                                        class="receipt-image rounded-lg w-full object-contain max-h-[600px] shadow-sm border border-gray-200 dark:border-gray-600 cursor-pointer hover:opacity-90" 
                                        alt="Receipt Preview"
                                        @click="showModal = true"
                                        onload="this.style.opacity='1'; this.style.transform='scale(1)'; document.getElementById('imageLoader').style.display='none';"
                                        style="opacity: 0; transform: scale(0.95); transition: opacity 0.3s ease-out, transform 0.3s ease-out;"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'; document.getElementById('imageLoader').style.display='none';"
                                        decoding="async"
                                        fetchpriority="high">
                                    
                                    <!-- Fallback ketika gambar gagal load -->
                                    <div class="hidden rounded-lg h-96 bg-gray-200 dark:bg-gray-700 items-center justify-center">
                                        <div class="text-center text-gray-500 dark:text-gray-400">
                                            <svg class="w-12 h-12 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                            </svg>
                                            <p class="text-sm">Gambar tidak dapat dimuat</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 space-y-2">
                                    <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                        Klik gambar untuk memperbesar
                                    </div>
                                    
                                    <!-- Info Quick Access -->
                                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border border-blue-200 dark:border-blue-800">
                                        <div class="flex items-start gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                            </svg>
                                            <div>
                                                <p class="text-xs font-medium text-blue-800 dark:text-blue-200">
                                                    Data Terdeteksi AI
                                                </p>
                                                <p class="text-xs text-blue-600 dark:text-blue-300 mt-1">
                                                    Periksa form di sebelah kanan untuk memastikan data sudah sesuai dengan struk
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Konfirmasi -->
                    <div class="lg:col-span-7" :class="{ 'lg:col-span-12': !showPreview }">
                        <div class="rounded-xl bg-white dark:bg-gray-800 p-6 ring-1 ring-gray-200 dark:ring-gray-700 transition-colors duration-200">
                            <!-- Toggle Preview Button (Mobile only) -->
                            <div class="lg:hidden mb-4">
                                <button type="button" @click="showPreview = !showPreview"
                                        class="inline-flex items-center gap-2 rounded-lg bg-gray-100 dark:bg-gray-700 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                    <span x-text="showPreview ? 'Sembunyikan Struk' : 'Lihat Struk'"></span>
                                </button>
                            </div>

                            <form method="POST" action="{{ route('transactions.confirm') }}" class="space-y-6"
                                id="form">
                                @csrf
                                <input type="hidden" name="receipt_path" value="{{ $prefill['receipt_path'] }}">
                                <input type="hidden" name="type" value="{{ $prefill['type'] }}">

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <x-input-label for="category_id" :value="__('Kategori')" />
                                        <x-select-input name="category_id" id="category_id" class="mt-1 w-full block">
                                            @foreach ($categories as $c)
                                                <option value="{{ $c->id }}" @selected($prefill['category_id'] == $c->id)>
                                                    {{ $c->name }}</option>
                                            @endforeach
                                        </x-select-input>
                                    </div>
                                    <div>
                                        <x-input-label for="date" :value="__('Tanggal')" />
                                        <x-text-input id="date" type="date" name="date" x-model="date"
                                            class="mt-1 block w-full" required />
                                    </div>
                                    <div>
                                        <x-input-label for="amount" :value="__('Total')" />
                                        <x-text-input id="amount" type="number" name="amount" x-model="amount"
                                            class="mt-1 block w-full" required />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="title" :value="__('Judul')" />
                                    <x-text-input id="title" type="text" name="title" x-model="title"
                                        class="mt-1 block w-full" required maxlength="100" />
                                </div>

                                <div>
                                    <x-input-label for="note" :value="__('Catatan')" />
                                    <x-textarea id="note" name="note" x-model="note" rows="2"
                                        class="mt-1 block w-full" />
                                </div>

                                <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Item Terdeteksi</h3>
                                        <button type="button" @click="addItem()"
                                            class="inline-flex items-center gap-1 rounded-lg bg-gray-900 dark:bg-gray-700 px-3 py-2 text-sm text-white hover:bg-black dark:hover:bg-gray-600 transition-colors duration-200">
                                            + Tambah Item
                                        </button>
                                    </div>

                                    <template x-for="(it, idx) in items" :key="idx">
                                        <div class="mt-3 grid grid-cols-1 md:grid-cols-12 gap-3">
                                            <div class="md:col-span-6">
                                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Nama Item</label>
                                                <input :name="`items[${idx}][name]`" x-model="it.name"
                                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Qty</label>
                                                <input type="number" step="0.01" :name="`items[${idx}][qty]`"
                                                    x-model.number="it.qty"
                                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200">
                                            </div>
                                            <div class="md:col-span-3">
                                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Harga</label>
                                                <input type="number" step="0.01" :name="`items[${idx}][price]`"
                                                    x-model.number="it.price"
                                                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors duration-200">
                                            </div>
                                            <div class="md:col-span-1 flex items-end">
                                                <button type="button" @click="removeItem(idx)"
                                                    class="w-full md:w-auto rounded-lg border border-red-300 dark:border-red-600 px-3 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Ringkasan kecil -->
                                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                                        <div class="rounded-lg bg-gray-50 dark:bg-gray-700 p-3 ring-1 ring-gray-200 dark:ring-gray-600 transition-colors duration-200">
                                            <div class="text-gray-600 dark:text-gray-400">Jumlah Item</div>
                                            <div class="text-gray-900 dark:text-gray-100 font-semibold" x-text="items.length"></div>
                                        </div>
                                        <div class="rounded-lg bg-gray-50 dark:bg-gray-700 p-3 ring-1 ring-gray-200 dark:ring-gray-600 transition-colors duration-200">
                                            <div class="text-gray-600 dark:text-gray-400">Subtotal Item</div>
                                            <div class="text-gray-900 dark:text-gray-100 font-semibold" x-text="currency(sumItems())">
                                            </div>
                                        </div>
                                        <div class="rounded-lg bg-gray-50 dark:bg-gray-700 p-3 ring-1 ring-gray-200 dark:ring-gray-600 transition-colors duration-200">
                                            <div class="text-gray-600 dark:text-gray-400">Total (Form)</div>
                                            <div class="text-gray-900 dark:text-gray-100 font-semibold" x-text="currency(amount||0)"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row items-center gap-3 pt-2">
                                    <x-primary-button class="w-full md:w-max" type="submit" id="submitBtn">
                                        <span id="iconbtn">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                        </span>
                                        <span id="spinner" class="hidden">
                                            <i class='bx bx-loader-alt bx-spin bx-rotate-90'></i>
                                        </span>
                                        <span id="textBtn">Simpan</span>
                                    </x-primary-button>
                                    <x-secondary-link class="w-full md:w-max"
                                        href="{{ route('transactions.create') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                        </svg>
                                        Kembali
                                    </x-secondary-link>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Lightbox untuk Preview Struk -->
        <div x-show="showModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-85 transition-opacity" @click="showModal = false"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Preview Struk</h3>
                            <button type="button" @click="showModal = false" class="bg-white dark:bg-gray-700 rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="text-center bg-gray-100 dark:bg-gray-900 rounded-lg p-4">
                            <img src="{{ asset('storage/' . $prefill['receipt_path']) }}" 
                                 class="max-w-full max-h-[70vh] object-contain mx-auto rounded-lg shadow-lg border border-gray-200 dark:border-gray-600" 
                                 alt="Receipt Full Size"
                                 style="image-rendering: -webkit-optimize-contrast;"
                                 loading="eager">
                        </div>
                        <div class="mt-3 text-center text-xs text-gray-500 dark:text-gray-400">
                            Gunakan scroll atau zoom browser untuk melihat detail lebih jelas
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" 
                                @click="showModal = false"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @push('styles')
            <style>
                /* Optimasi untuk loading gambar */
                .receipt-image {
                    will-change: opacity, transform;
                    image-rendering: -webkit-optimize-contrast;
                    image-rendering: optimize-contrast;
                    backface-visibility: hidden;
                    transform: translateZ(0);
                    content-visibility: auto;
                }
                
                /* Preload spinner yang ringan */
                .loading-spinner {
                    width: 40px;
                    height: 40px;
                    border: 4px solid #f3f4f6;
                    border-top: 4px solid #3b82f6;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                }
                
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                
                /* Optimasi transisi */
                .fade-in {
                    animation: fadeIn 0.3s ease-in;
                }
                
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
            </style>
        @endpush

        @push('scripts')
            <script>
                // Optimasi preload gambar
                document.addEventListener('DOMContentLoaded', function() {
                    // Preload gambar secara aggressive
                    const receiptImage = new Image();
                    receiptImage.src = '{{ asset('storage/' . $prefill['receipt_path']) }}';
                    
                    // Cache gambar di browser
                    receiptImage.onload = function() {
                        console.log('Receipt image preloaded successfully');
                    };
                });
                
                function reviewExtracted(prefill) {
                    return {
                        showModal: false,
                        showPreview: true, // default show di mobile
                        date: prefill.date,
                        amount: prefill.amount,
                        title: prefill.title,
                        note: prefill.note,
                        items: prefill.items || [],
                        addItem() {
                            this.items.push({
                                name: '',
                                qty: 1,
                                price: null
                            });
                        },
                        removeItem(i) {
                            this.items.splice(i, 1);
                        },
                        sumItems() {
                            return this.items.reduce((s, it) => {
                                const q = Number(it.qty || 0),
                                    p = Number(it.price || 0);
                                return s + (q * p);
                            }, 0);
                        },
                        currency(n) {
                            try {
                                return new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    maximumFractionDigits: 0
                                }).format(n);
                            } catch (e) {
                                return 'Rp ' + (n || 0).toLocaleString('id-ID');
                            }
                        }
                    }
                }

                document.getElementById('form').addEventListener('submit', function(e) {
                    const btn = document.getElementById('submitBtn');
                    const iconbtn = document.getElementById('iconbtn');
                    const spinner = document.getElementById('spinner');
                    const textBtn = document.getElementById('textBtn');

                    textBtn.innerText = 'Memproses...';
                    spinner.classList.remove('hidden');

                    iconbtn.classList.add('hidden');

                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                });
            </script>
        @endpush
    </div>
</x-app-layout>
