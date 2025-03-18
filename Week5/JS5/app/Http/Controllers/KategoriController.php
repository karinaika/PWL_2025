<?php

namespace App\Http\Controllers;

use App\Models\KategoriModel;
use Illuminate\Http\Request;
use App\DataTables\KategoriDataTable;

class KategoriController extends Controller
{
    public function index(KategoriDataTable $dataTable)
    {
        return $dataTable->render('kategori.index');
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kodeKategori' => 'required|max:10',
            'namaKategori' => 'required|max:100',
        ]);

        KategoriModel::create([
            'kategori_kode' => $request->kodeKategori,
            'kategori_nama' => $request->namaKategori,
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = KategoriModel::findOrFail($id);
        return view('kategori.edit', ['kategori' => $data]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kodeKategori' => 'required',
            'namaKategori' => 'required'
        ]);

        $kategori = KategoriModel::findOrFail($id);

        // Update data
        $kategori->kategori_kode = $request->kodeKategori;
        $kategori->kategori_nama = $request->namaKategori;

        $kategori->save();

        return redirect('/kategori');
    }

    public function delete($id){
        KategoriModel::where('kategori_id',$id)->delete();
        return redirect('/kategori');
    }

}