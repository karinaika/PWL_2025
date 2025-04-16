<?php

namespace App\Http\Controllers;

use App\Models\StokModel;
use App\Models\BarangModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StokController extends Controller
{
    // Menampilkan halaman stok
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Data Stok Barang',
            'list' => ['Home', 'Stok']
        ];

        $page = (object) [
            'title' => 'Daftar stok barang yang tercatat'
        ];

        $activeMenu = 'stok';

        $stok = StokModel::with(['barang', 'user'])->get(); // ambil data stok dengan relasi barang & user
        $barang = BarangModel::all();

        return view('stok.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'stok' => $stok,
            'barang' => $barang,
            'activeMenu' => $activeMenu
        ]);
    }

    public function list(Request $request)
    {
        $stok = StokModel::with(['barang', 'user']);

        if ($request->barang_id) {
            $stok->where('barang_id', $request->barang_id);
        }

        return DataTables::of($stok)
            ->addIndexColumn()
            ->addColumn('barang_kode', function ($s) {
                return $s->barang->barang_kode ?? '-';
            })
            ->addColumn('barang_nama', function ($s) {
                return $s->barang->barang_nama ?? '-';
            })
            ->addColumn('user_nama', function ($s) {
                return $s->user->nama ?? '-';
            })
            ->addColumn('aksi', function ($s) {
                // menambahkan kolom aksi
                //$btn = '<a href="' . url('/stok/' . $s->stok_id) . '" class="btn btn-info btn-sm">Detail</a> ';
                // $btn .= '<a href="' . url('/level/' . $level->level_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
                // $btn .= '<form class="d-inline-block" method="POST" action="' . url('/level/' . $level->level_id) . '">'
                //     . csrf_field() . method_field('DELETE') .
                //     '<button type="submit" class="btn btn-danger btn-sm"
                //     onclick="return confirm(\'Apakah Anda yakit menghapus data
                //     ini?\');">Hapus</button></form>';
    
                $btn = '<button onclick="modalAction(\'' . url('/stok/' . $s->stok_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $s->stok_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $s->stok_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    // Menampilkan form tambah stok (Ajax)
    public function createAjax()
    {
        return view('stok.create_ajax');
    }

    // Menyimpan data stok yang baru
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required',
            'jumlah_stok' => 'required|integer',
        ]);

        $stok = new StokModel();
        $stok->barang_id = $request->barang_id;
        $stok->stok_jumlah = $request->jumlah_stok;
        $stok->user_id = auth()->user()->id;
        $stok->stok_tanggal = now();
        $stok->save();

        return response()->json(['success' => 'Stok berhasil ditambahkan']);
    }
}
