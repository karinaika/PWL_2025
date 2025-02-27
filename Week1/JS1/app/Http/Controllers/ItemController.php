<?php

namespace App\Http\Controllers; // Menentukan namespace untuk controller

use App\Models\Item; // Menggunakan model Item untuk interaksi dengan database
use Illuminate\Http\Request; // Mengimpor Request untuk menangani input dari form

class ItemController extends Controller // kelas ItemController turunan dari Controller
{
    /**
     * Menampilkan daftar semua item.
     */
    public function index()
    {
        $items = Item::all(); // Mengambil semua data dari tabel items
        return view('items.index', compact('items')); // Menampilkan view items.index dan mengirim data items ke dalamnya
    }

    /**
     * Menampilkan form untuk menambah item baru.
     */
    public function create()
    {
        return view('items.create'); // Mengembalikan view items.create untuk form tambah item
    }

    /**
     * Menyimpan data item baru ke dalam database.
     */
    public function store(Request $request)
    {
        // Memvalidasi input agar name dan description wajib diisi
        $request->validate([
            'name' => 'required',
            'description' => 'required'
        ]);

        //Item::create($request->all());
        //return redirect()->route('items.index');

        // Menyimpan data item ke dalam database hanya dengan atribut yang diizinkan
        Item::create($request->only(['name', 'description']));

        // Redirect ke halaman daftar item dengan pesan sukses
        return redirect()->route('items.index')->with('success', 'Item added successfully.');
    }

    /**
     * Menampilkan detail dari satu item berdasarkan ID.
     */
    public function show(Item $item)
    {
        return view('items.show', compact('item')); // Menampilkan halaman detail item
    }

    /**
     * Menampilkan form untuk mengedit item yang sudah ada.
     */
    public function edit(Item $item)
    {
        return view('items.edit', compact('item')); // Mengembalikan view items.edit untuk mengedit item tertentu
    }

    /**
     * Memperbarui data item yang sudah ada di database.
     */
    public function update(Request $request, Item $item)
    {
        // Memvalidasi input agar name dan description wajib diisi
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        //$item->update($request->all());
        //return redirect()->route('items.index);

        // Hanya masukan atribut yang diizinkan
        $item->update($request->only(['name', 'description'])); // Memperbarui data item hanya dengan atribut yang diizinkan

        // Redirect ke halaman daftar item dengan pesan sukses
        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    /**
     * Menghapus item dari database.
     */
    public function destroy(Item $item)
    {
        //return redirect()->route('items.index);
        $item->delete(); // Menghapus item dari database

        // Redirect ke halaman daftar item dengan pesan sukses
        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }
}
