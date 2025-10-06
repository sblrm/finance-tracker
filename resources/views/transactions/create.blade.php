<x-app-layout>
    <div x-data="createTransaction({
        typeInitial: '{{ old('type', $defaultType ?? 'expense') }}',
        categories: @js($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'type' => $c->type])),
        oldCategoryId: '{{ old('category_id') }}'
    })">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100">Tambah Transaksi</h2>
        </x-slot>

        <div>
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="rounded-xl bg-white dark:bg-gray-800 p-6 ring-1 ring-gray-200 dark:ring-gray-700 transition-colors duration-200">

                    {{-- ERROR HANDLE --}}
                    @if ($errors->any())
                        <div class="mb-6 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 ring-1 ring-red-200 dark:ring-red-800">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- TYPE TOGGLE --}}
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Pilih tipe transaksi:</div>
                        <div class="inline-flex rounded-xl bg-gray-100 dark:bg-gray-700 p-1">
                            <button type="button" @click="type='expense'"
                                :class="type === 'expense' ? 'bg-blue-100 dark:bg-blue-900/30 text-gray-900 dark:text-blue-300 border-blue-300 dark:border-blue-600' :
                                    'text-gray-600 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200 border-transparent'"
                                class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-xl border transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 6 9 12.75l4.286-4.286a11.948 11.948 0 0 1 4.306 6.43l.776 2.898m0 0 3.182-5.511m-3.182 5.51-5.511-3.181" />
                                </svg>
                                Expense
                            </button>
                            <button type="button" @click="type='income'"
                                :class="type === 'income' ? 'bg-blue-100 dark:bg-blue-900/30 text-gray-900 dark:text-blue-300 border-blue-300 dark:border-blue-600' :
                                    'text-gray-600 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200 border-transparent'"
                                class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-xl border transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                                </svg> Income
                            </button>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="mt-4 flex gap-1 rounded-xl bg-gray-100 dark:bg-gray-700 p-1 w-full sm:w-auto">
                        <button type="button" @click="tab='manual'"
                            :class="tab === 'manual' ? 'bg-blue-100 dark:bg-blue-900/30 border-blue-200 dark:border-blue-600 text-gray-900 dark:text-blue-300' :
                                'border-transparent text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-200'"
                            class="w-full flex items-center justify-center gap-2 border rounded-xl px-4 py-2 text-sm sm:text-base font-medium transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg> Input Manual
                        </button>
                        <button type="button" @click="tab='struk'"
                            :class="tab === 'struk' ? 'bg-blue-100 dark:bg-blue-900/30 border-blue-200 dark:border-blue-600 text-gray-900 dark:text-blue-300' :
                                'border-transparent text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-200'"
                            class="w-full flex items-center justify-center gap-2 border rounded-xl px-4 py-2 text-sm sm:text-base font-medium transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                            </svg> Dari Foto Struk (AI)
                        </button>
                    </div>

                    <!-- Panel: Manual -->
                    <div x-show="tab==='manual'" x-cloak class="mt-6">
                        <form method="POST" action="{{ route('transactions.store') }}" class="space-y-6"
                            id="form">
                            @csrf
                            <input type="hidden" name="type" :value="type">

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <x-input-label for="category_id" value="Kategori" />
                                    <x-select-input id="category_id" name="category_id" class="w-full block mt-1"
                                        x-model="category_id" required>
                                        <option value="">— Pilih —</option>
                                        <template x-for="c in filteredCats" :key="c.id">
                                            <option :value="c.id" x-text="c.name"></option>
                                        </template>
                                    </x-select-input>

                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="date" value="Tanggal" />
                                    <x-text-input id="date" type="date" name="date" class="w-full block mt-1"
                                        :value="old('date', now()->toDateString())" required />
                                    <x-input-error :messages="$errors->get('date')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="nominal" value="Nominal" />
                                    <x-text-input id="nominal" type="number" name="amount" step="0.01"
                                        class="w-full block mt-1" required />
                                    <x-input-error :messages="$errors->get('nominal')" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="title" value="Nama atau Judul Transaksi" />
                                <x-text-input id="title" type="text" name="title" class="w-full block mt-1"
                                    required />
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="note" value="Catatan (Opsinal)" />
                                <x-textarea id="note" type="text" name="note"
                                    class="w-full block mt-1"></x-textarea>
                                <x-input-error :messages="$errors->get('note')" class="mt-2" />
                            </div>

                            <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Item (opsional)</h3>
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
                                                class="w-full mt-1 block border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 rounded-xl py-3 px-4 shadow-sm">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Qty</label>
                                            <input type="number" step="0.01" :name="`items[${idx}][qty]`"
                                                x-model.number="it.qty"
                                                class="w-full mt-1 block border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 rounded-xl py-3 px-4 shadow-sm">
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Harga</label>
                                            <input type="number" step="0.01" :name="`items[${idx}][price]`"
                                                x-model.number="it.price"
                                                class="w-full mt-1 block border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 rounded-xl py-3 px-4 shadow-sm">
                                        </div>
                                        <div class="md:col-span-1 flex items-end">
                                            <button type="button" @click="removeItem(idx)"
                                                class="w-full md:w-auto rounded-lg border border-red-300 dark:border-red-600 px-3 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200">
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="flex flex-col md:flex-row items-center gap-3 pt-2">
                                <x-primary-button class="w-full md:w-max" id="submitBtn">
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
                                <x-secondary-link class="w-full md:w-max" href="{{ route('transactions.index') }}">
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

                    <!-- Panel: Struk -->
                    <div x-show="tab==='struk'" x-cloak class="mt-6">
                        <form method="POST" action="{{ route('transactions.extract') }}"
                            enctype="multipart/form-data" class="space-y-6" id="form2">
                            @csrf
                            <input type="hidden" name="type" :value="type">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="category_id" value="Kategori" />
                                    <x-select-input id="category_id" name="category_id" class="w-full block mt-1"
                                        x-model="category_id" required>
                                        <option value="">— Pilih —</option>
                                        <template x-for="c in filteredCats" :key="c.id">
                                            <option :value="c.id" x-text="c.name"></option>
                                        </template>
                                    </x-select-input>

                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="title" value="Nama atau Judul Transaksi" />
                                    <x-text-input id="title" type="text" name="title"
                                        class="w-full block mt-1" required />
                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Foto Struk</label>

                                    <div x-data="receiptUploader()" class="mt-2">
                                        <!-- input asli (disembunyikan) -->
                                        <input x-ref="file" type="file" name="receipt" accept="image/*"
                                            class="sr-only" @change="fileChosen" required>

                                        <!-- Placeholder / Dropzone -->
                                        <div x-show="!previewUrl" x-cloak @click="$refs.file.click()"
                                            @dragover.prevent="drag=true" @dragleave.prevent="drag=false"
                                            @drop.prevent="drop($event)"
                                            :class="drag ? 'border-indigo-500 dark:border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20' :
                                                'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500'"
                                            class="relative cursor-pointer rounded-2xl border-2 border-dashed transition-colors bg-gray-50 dark:bg-gray-800/50">
                                            <div class="absolute inset-0 grid place-items-center p-8 text-center">
                                                <div class="flex flex-col items-center">
                                                    <!-- icon -->
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-12 w-12 text-gray-400 dark:text-gray-500" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="1.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M3 16.5V8.25A2.25 2.25 0 015.25 6h3A2.25 2.25 0 0110.5 8.25v.75m0 0A2.25 2.25 0 0012.75 11.25h.75m-3-2.25l2.25-2.25 2.25 2.25M21 16.5V12a2.25 2.25 0 00-2.25-2.25H15" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M3 16.5A2.25 2.25 0 005.25 18.75h13.5A2.25 2.25 0 0021 16.5M3 16.5v1.5A2.25 2.25 0 005.25 20.25h13.5A2.25 2.25 0 0021 18v-1.5" />
                                                    </svg>

                                                    <p class="mt-3 text-sm text-gray-700 dark:text-gray-300">
                                                        <span class="text-indigo-600 dark:text-indigo-400 underline underline-offset-4">
                                                            klik untuk pilih
                                                        </span>
                                                    </p>
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: JPG/PNG · Maks 5MB
                                                    </p>

                                                    <span
                                                        class="mt-4 inline-flex items-center gap-2 rounded-lg bg-gray-900 dark:bg-gray-700 px-3 py-2 text-sm font-medium text-white shadow hover:bg-black dark:hover:bg-gray-600 transition-colors duration-200">
                                                        Pilih Foto
                                                    </span>
                                                </div>
                                            </div>
                                            <!-- spacer agar tinggi enak dilihat -->
                                            <div class="invisible">
                                                <img class="h-64 w-full object-cover" alt="">
                                            </div>
                                        </div>

                                        <!-- Preview -->
                                        <div x-show="previewUrl" x-cloak
                                            class="relative overflow-hidden rounded-2xl ring-1 ring-gray-200 dark:ring-gray-700">
                                            <img :src="previewUrl" alt="Preview struk"
                                                class="h-72 w-full object-cover" />
                                            <div
                                                class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/40 via-transparent">
                                            </div>

                                            <!-- info & actions -->
                                            <div
                                                class="absolute bottom-0 left-0 right-0 flex items-center justify-between gap-3 p-3">
                                                <div class="min-w-0">
                                                    <div class="truncate text-sm font-medium text-white/90"
                                                        x-text="fileName"></div>
                                                    <div class="truncate text-xs text-white/70" x-text="prettySize()">
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <button type="button" @click="$refs.file.click()"
                                                        class="rounded-lg bg-white/90 px-3 py-1.5 text-xs font-medium text-gray-900 shadow hover:bg-white">
                                                        Ganti
                                                    </button>
                                                    <button type="button" @click="remove()"
                                                        class="rounded-lg bg-red-600/90 px-3 py-1.5 text-xs font-medium text-white shadow hover:bg-red-600">
                                                        Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- error -->
                                        <p x-show="error" x-text="error" class="mt-2 text-sm text-red-600"></p>

                                        @error('receipt')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                            <template x-if="previewUrl">
                                <img :src="previewUrl"
                                    class="rounded-lg ring-1 ring-gray-200 max-h-72 object-contain" alt="Preview">
                            </template>

                            <div class="flex flex-col md:flex-row items-center gap-3 pt-2">
                                <x-primary-button class="w-full md:w-max" type="submit" id="submitBtn2">
                                    <span id="iconbtn2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                    </span>
                                    <span id="spinner2" class="hidden">
                                        <i class='bx bx-loader-alt bx-spin bx-rotate-90'></i>
                                    </span>
                                    <span id="textBtn2">Proses dengan AI</span>
                                </x-primary-button>
                                <x-secondary-link class="w-full md:w-max" href="{{ route('transactions.index') }}">
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

        @push('scripts')
            <script>
                function createTransaction(init) {
                    return {
                        tab: 'manual',
                        type: init.typeInitial || 'expense',
                        categories: init.categories || [],
                        category_id: init.oldCategoryId || '',
                        items: [],
                        previewUrl: null,

                        get filteredCats() {
                            return this.categories.filter(c => c.type === this.type);
                        },

                        init() {
                            // Auto-set category pertama saat type berubah jika category_id tidak valid
                            this.$watch('type', () => {
                                if (!this.filteredCats.find(c => String(c.id) === String(this.category_id))) {
                                    this.category_id = this.filteredCats[0]?.id ?? '';
                                }
                            });
                            // Inisialisasi awal
                            if (!this.filteredCats.find(c => String(c.id) === String(this.category_id))) {
                                this.category_id = this.filteredCats[0]?.id ?? '';
                            }
                        },

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
                        onPreview(e) {
                            const f = e.target.files?.[0];
                            if (!f) return;
                            this.previewUrl = URL.createObjectURL(f);
                        }
                    }
                }

                function receiptUploader() {
                    return {
                        previewUrl: null,
                        fileName: '',
                        fileSize: 0,
                        drag: false,
                        error: '',
                        maxSize: 5 * 1024 * 1024, // 5MB

                        fileChosen(e) {
                            const file = e.target.files?.[0];
                            this.handleFile(file);
                        },
                        drop(e) {
                            this.drag = false;
                            const file = e.dataTransfer.files?.[0];
                            this.handleFile(file);
                        },
                        handleFile(file) {
                            if (!file) return;
                            this.error = '';

                            if (!file.type.startsWith('image/')) {
                                this.error = 'File harus berupa gambar (JPG/PNG).';
                                return this.clear();
                            }
                            if (file.size > this.maxSize) {
                                this.error = 'Ukuran maksimal 5MB.';
                                return this.clear();
                            }

                            this.fileName = file.name;
                            this.fileSize = file.size;
                            this.previewUrl = URL.createObjectURL(file);
                        },
                        prettySize() {
                            if (!this.fileSize) return '';
                            const mb = this.fileSize / (1024 * 1024);
                            return mb.toFixed(2) + ' MB';
                        },
                        remove() {
                            this.clear();
                        },
                        clear() {
                            this.previewUrl = null;
                            this.fileName = '';
                            this.fileSize = 0;
                            if (this.$refs.file) this.$refs.file.value = '';
                        }
                    }
                }

                document.getElementById('form').addEventListener('submit', function() {
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

                document.getElementById('form2').addEventListener('submit', function() {
                    const btn = document.getElementById('submitBtn2');
                    const iconbtn = document.getElementById('iconbtn2');
                    const spinner = document.getElementById('spinner2');
                    const textBtn = document.getElementById('textBtn2');

                    textBtn.innerText = 'Memproses AI... (bisa 1-2 menit)';
                    spinner.classList.remove('hidden');

                    iconbtn.classList.add('hidden');

                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                });
            </script>
        @endpush
    </div>
</x-app-layout>
