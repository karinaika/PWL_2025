<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarangModel;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    public function index()
    {
        $barang = BarangModel::all();
        return response()->json([
            'success' => true,
            'data' => $barang,
        ]);
    }

    public function store(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'kategori_id'  => 'required|exists:m_kategori,kategori_id',
            'barang_kode'  => 'required|unique:m_barang,barang_kode',
            'barang_nama'  => 'required',
            'harga_beli'   => 'required|numeric',
            'harga_jual'   => 'required|numeric',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Upload image kalau ada
        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = $request->image->hashName();
            $request->image->storeAs('public/barang', $imageName);
        }

        $barang = BarangModel::create([
            'kategori_id' => $request->kategori_id,
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_beli'  => $request->harga_beli,
            'harga_jual'  => $request->harga_jual,
            'image'       => $imageName,
        ]);

        return response()->json([
            'success' => true,
            'data' => $barang,
        ], 201);
    }

    public function show($id)
    {
        $barang = BarangModel::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $barang,
        ]);
    }

    public function update(Request $request, $id)
    {
        $barang = BarangModel::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan',
            ], 404);
        }

        // Validasi
        $validator = Validator::make($request->all(), [
            'kategori_id'  => 'sometimes|required|exists:m_kategori,kategori_id',
            'barang_kode'  => 'sometimes|required|unique:m_barang,barang_kode,' . $id . ',barang_id',
            'barang_nama'  => 'sometimes|required',
            'harga_beli'   => 'sometimes|required|numeric',
            'harga_jual'   => 'sometimes|required|numeric',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Upload image kalau ada
        if ($request->hasFile('image')) {
            $imageName = $request->image->hashName();
            $request->image->storeAs('public/barang', $imageName);
            $barang->image = $imageName;
        }

        $barang->update($request->except('image')); // Kecuali image, karena sudah dihandle manual
        $barang->save();

        return response()->json([
            'success' => true,
            'data' => $barang,
        ]);
    }

    public function destroy($id)
    {
        $barang = BarangModel::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan',
            ], 404);
        }

        $barang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Barang berhasil dihapus',
        ]);
    }
}