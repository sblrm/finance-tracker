<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('transactions')->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'name' => 'unique:categories|required|string|max:255',
        ], [
            'type.required' => 'Tipe kategori harus diisi.',
            'type.in' => 'Tipe kategori tidak valid.',
            'name.required' => 'Nama kategori harus diisi.',
            'name.unique' => 'Nama kategori sudah ada.',
        ]);

        Category::create($request->only('type', 'name'));

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'name' => 'unique:categories,name,' . $category->id . '|required|string|max:255',
        ], [
            'type.required' => 'Tipe kategori harus diisi.',
            'type.in' => 'Tipe kategori tidak valid.',
            'name.required' => 'Nama kategori harus diisi.',
            'name.unique' => 'Nama kategori sudah ada.',
        ]);

        $category->update($request->only('type', 'name'));

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->transactions()->count() > 0) {
            return redirect()->route('categories.index')->with('error', 'Tidak bisa menghapus kategori yang memiliki transaksi.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
