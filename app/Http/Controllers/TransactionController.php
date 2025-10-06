<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\GeminiReceiptService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('category', 'items')
            ->where('user_id', Auth::user()->id)
            ->latest()->paginate(12);

        $totalMonthly = Transaction::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        return view('transactions.index', compact('transactions', 'totalMonthly'));
    }

    public function create()
    {
        $categories = Category::select(['id', 'name', 'type'])
            ->orderBy('name')->get();
        $defaultType = 'expense';

        return view('transactions.create', compact('categories'));
    }

    /**
     * Flow manual (tanpa struk)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'title' => 'required|string|max:100',
            'note' => 'nullable|string',
            'items.*.name' => 'nullable|string',
            'items.*.qty' => 'nullable|numeric',
            'items.*.price' => 'nullable|numeric',
        ], [
            'type.requuired' => 'Tipe transaksi wajib diisi.',
            'type.in' => 'Tipe transaksi tidak valid.',
            'category_id.required' => 'Kategori wajib diisi.',
            'category_id.exists' => 'Kategori tidak ditemukan.',
            'date.required' => 'Tanggal wajib diisi.',
            'date.date' => 'Tanggal tidak valid.',
            'amount.required' => 'Jumlah wajib diisi.',
            'amount.numeric' => 'Jumlah harus berupa angka.',
            'title.required' => 'Judul wajib diisi.',
            'title.string' => 'Judul harus berupa teks.',
            'title.max' => 'Judul maksimal 100 karakter.',
            'note.string' => 'Keterangan harus berupa teks.',
            'items.*.name.string' => 'Nama item harus berupa teks.',
            'items.*.qty.numeric' => 'Kuantitas item harus berupa angka.',
            'items.*.price.numeric' => 'Harga item harus berupa angka.',
        ]);

        $catType = Category::where('id', $validated['category_id'])->value('type');
        if ($catType !== $validated['type']) {
            return back()->withInput()->withErrors(['category_id' => 'Kategori tidak sesuai dengan tipe transaksi.']);
        }

        DB::transaction(function () use ($validated) {
            $tx = Transaction::create([
                'user_id'     => Auth::user()->id,
                'category_id' => $validated['category_id'],
                'title'       => $validated['title'],
                'type'        => $validated['type'],
                'date'        => $validated['date'],
                'amount'      => $validated['amount'],
                'note' => $validated['note'] ?? null,
            ]);

            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $it) {
                    if (!empty($it['name'])) {
                        Item::create([
                            'transaction_id' => $tx->id,
                            'name'           => $it['name'],
                            'quantity'       => $it['qty'] ?? 1,
                            'price'          => $it['price'] ?? 0,
                            'subtotal'       => ($it['qty'] ?? 1) * ($it['price'] ?? 0),
                        ]);
                    }
                }
            }
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi tersimpan.');
    }

    /**
     * Step 1 (via struk): upload → ekstraksi Gemini → tampilkan review
     */
    public function extractFromReceipt(Request $request, GeminiReceiptService $ai)
    {
        // Increase execution time for AI processing
        set_time_limit(120); // 2 minutes
        
        $validated = $request->validate([
            'type'        => 'required|in:income,expense',
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:100',
            'receipt'     => 'required|image|max:5120', // 5MB
        ]);

        $catType = Category::where('id', $validated['category_id'])->value('type');
        if ($catType !== $validated['type']) {
            return back()->withInput()->withErrors(['category_id' => 'Kategori tidak sesuai dengan tipe transaksi.']);
        }

        $path = $request->file('receipt')->store('receipts', 'public');
        
        // Generate unique session key for this extraction
        $sessionKey = uniqid('extract_', true);
        
        try {
            // Set time limit for this operation
            set_time_limit(300); // 5 minutes max for AI processing
            
            $absolute = Storage::disk('public')->path($path);
            $result = $ai->extract($absolute);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($path);

            Log::warning('Gemini extract failed', [
                'msg' => $e->getMessage(),
                'file_size' => file_exists($absolute ?? '') ? filesize($absolute) : 'unknown',
                'file_path' => $path ?? 'unknown'
            ]);
            
            // More specific error messages based on exception
            $errorMsg = match(true) {
                str_contains($e->getMessage(), 'timeout') => 'Proses AI timeout - gambar mungkin terlalu besar. Coba kompres gambar atau isi manual.',
                str_contains($e->getMessage(), 'terhubung') => 'Tidak bisa terhubung ke layanan AI. Periksa koneksi internet.',
                str_contains($e->getMessage(), 'banyak permintaan') => 'Terlalu banyak permintaan. Tunggu sebentar lalu coba lagi.',
                str_contains($e->getMessage(), 'bermasalah') => 'Server AI sedang bermasalah. Coba lagi nanti.',
                default => 'Proses AI gagal. Coba lagi atau isi manual. Error: ' . substr($e->getMessage(), 0, 100)
            };
            
            return back()
                ->withInput() // kembalikan semua input (file perlu pilih ulang)
                ->with('error', $errorMsg)
                ->with('ai_tab', 'struk'); // supaya tab "Struk" tetap terbuka
        }

        // Siapkan data untuk halaman review
        $prefill = [
            'type'        => $validated['type'],
            'category_id' => $validated['category_id'],
            'date'        => $result['datetime'] ? Carbon::parse($result['datetime'])->toDateString() : now()->toDateString(),
            'amount'      => $result['total'] ?? null,
            'title'       => $validated['title'],
            'note'        => $result['merchant'] ? "Belanja: {$result['merchant']}" : null,
            'receipt_path' => $path,
            'items'       => array_map(function ($it) {
                $price = $it['unit_price'] ?? ($it['subtotal'] ?? 0);
                return [
                    'name'  => $it['name'],
                    'qty'   => $it['qty'],
                    'price' => $price,
                    'subtotal' => $it['subtotal'] ?? ($price * $it['qty']),
                ];
            }, $result['items'] ?? []),
        ];

        $categories = Category::orderBy('name')->get();

        return view('transactions.review', compact('prefill', 'categories'));
    }

    /**
     * Step 2 (via struk): user konfirmasi & simpan final
     */
    public function confirmExtracted(Request $request)
    {
        $validated = $request->validate([
            'type'          => 'required|in:income,expense',
            'category_id'   => 'required|exists:categories,id',
            'date'          => 'required|date',
            'amount'        => 'required|numeric',
            'title'         => 'required|string|max:100',
            'note'          => 'nullable|string',
            'receipt_path'  => 'nullable|string',
            'items.*.name'  => 'nullable|string',
            'items.*.qty'   => 'nullable|numeric',
            'items.*.price' => 'nullable|numeric',
        ]);
        $catType = Category::where('id', $validated['category_id'])->value('type');
        if ($catType !== $validated['type']) {
            return back()->withInput()->withErrors(['category_id' => 'Kategori tidak sesuai dengan tipe transaksi.']);
        }

        DB::transaction(function () use ($validated) {
            $tx = Transaction::create([
                'user_id'     => Auth::user()->id,
                'category_id' => $validated['category_id'],
                'type'        => $validated['type'],
                'date'        => $validated['date'],
                'amount'      => $validated['amount'],
                'title'       => $validated['title'],
                'note' => $validated['note'] ?? null,
                'image' => $validated['receipt_path'] ?? null,
                'gemini_data' => $validated,
            ]);

            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $it) {
                    if (!empty($it['name'])) {
                        $qty   = (float)($it['qty'] ?? 1);
                        $price = (float)($it['price'] ?? 0);
                        Item::create([
                            'transaction_id' => $tx->id,
                            'name'           => $it['name'],
                            'quantity'       => $qty,
                            'price'          => $price,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi dari struk tersimpan.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('category', 'items');

        if ($transaction->user_id !== Auth::user()->id) {
            return redirect()->route('transactions.index')->with('error', 'Anda tidak memiliki akses ke transaksi ini.');
        }

        return view('transactions.show', [
            'tx' => $transaction,
        ]);
    }
}
