<?php

namespace App\Http\Controllers;

use App\Models\StokModel;
use App\Models\BarangModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Barryvdh\DomPDF\Facade\Pdf;

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

    //Menampilkan form import stok
    public function import()
    {
        return view('stok.import');
    }

    // Import AJAX
    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_stok' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_stok');
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);

            $insert = [];
            if (count($data) > 1) {
                foreach ($data as $baris => $value) {
                    if ($baris > 1) {
                        $insert[] = [
                            'barang_id' => $value['A'],
                            'user_id' => $value['B'],
                            'stok_jumlah' => $value['C'],
                            'stok_tanggal' => now(),
                            'created_at' => now(),
                        ];
                    }
                }

                if (count($insert) > 0) {
                    StokModel::insertOrIgnore($insert);
                    return response()->json([
                        'status' => true,
                        'message' => 'Data berhasil diimport'
                    ]);
                }
            }

            return response()->json([
                'status' => false,
                'message' => 'Tidak ada data yang diimport'
            ]);
        }
        return redirect('/');
    }

    // export excel
    public function export_excel()
    {
        $stoks = StokModel::select('stok_id', 'barang_id', 'user_id', 'stok_jumlah', 'stok_tanggal')
            ->with(['barang', 'user'])
            ->orderBy('stok_id')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'ID Stok');
        $sheet->setCellValue('C1', 'Kode Barang');
        $sheet->setCellValue('D1', 'Nama Barang');
        $sheet->setCellValue('E1', 'Petugas');
        $sheet->setCellValue('F1', 'Jumlah Stok');
        $sheet->setCellValue('G1', 'Tanggal ');

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;
        foreach ($stoks as $stok) {
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $stok->stok_id);
            $sheet->setCellValue('C' . $baris, $stok->barang ? $stok->barang->barang_kode : '-');
            $sheet->setCellValue('D' . $baris, $stok->barang ? $stok->barang->barang_nama : '-');
            $sheet->setCellValue('E' . $baris, $stok->user ? $stok->user->nama : '-');
            $sheet->setCellValue('F' . $baris, $stok->stok_jumlah);
            $sheet->setCellValue('G' . $baris, $stok->stok_tanggal);
            $baris++;
            $no++;
        }

        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $sheet->setTitle('Data Stok');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Stok_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    // create AJAX
    public function create_ajax()
    {
        $barang = BarangModel::all();
        $user = UserModel::all();
        return view('stok.create_ajax', ['barang' => $barang, 'user' => $user]);
    }

    // export pdf
    public function export_pdf()
    {
        $stok = StokModel::select('stok_id', 'barang_id', 'user_id', 'stok_jumlah', 'stok_tanggal')
            ->with(['barang', 'user'])
            ->orderBy('stok_id')
            ->get();

        $pdf = Pdf::loadView('stok.export_pdf', ['stok' => $stok]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data_Stok_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    //Show AJAX
    public function show_ajax(string $id)
    {
        $stok = StokModel::find($id);
        return view('stok.show_ajax', ['stok' => $stok]);
    }

    // Store AJAX
    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_id' => 'required|integer|exists:m_barang,barang_id',
                'user_id' => 'required|integer|exists:m_user,user_id',
                'stok_jumlah' => 'required|integer|min:1',
                'stok_tanggal' => 'required|date'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            $existingStok = StokModel::where('barang_id', $request->barang_id)->first();
            if ($existingStok) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data stok untuk barang ini sudah ada. Silakan edit data yang tersedia.',
                ]);
            }

            StokModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data stok berhasil disimpan',
            ]);
        }
        return redirect('/');
    }

    // Edit AJAX
    public function edit_ajax(string $id)
    {
        $stok = StokModel::find($id);
        $barang = BarangModel::all();
        $user = UserModel::all();

        return view('stok.edit_ajax', [
            'stok' => $stok,
            'barang' => $barang,
            'user' => $user
        ]);
    }


    // Update AJAX
    public function update_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'barang_id' => 'required|integer|exists:m_barang,barang_id',
                'user_id' => 'required|integer|exists:m_user,user_id',
                'stok_jumlah' => 'required|integer|min:1',
                'stok_tanggal' => 'required|date'
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            $existingStok = StokModel::where('barang_id', $request->barang_id)
                ->where('stok_id', '!=', $id)
                ->first();

            if ($existingStok) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data stok untuk barang ini sudah ada. Silakan edit data yang sudah ada.',
                ]);
            }

            $stok = StokModel::find($id);
            if ($stok) {
                $stok->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data stok berhasil diubah',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data stok tidak ditemukan',
                ]);
            }
        }
        return redirect('/');
    }

    // Confirmasi AJAX
    public function confirm_ajax(string $id)
    {
        $stok = StokModel::with(['barang', 'user'])->find($id);
        return view('stok.confirm_ajax', ['stok' => $stok]);
    }

    // Delete AJAX
    public function delete_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $stok = StokModel::find($id);
            if ($stok) {
                $stok->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data stok berhasil dihapus',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data stok tidak ditemukan',
                ]);
            }
        }
        return redirect('/');
    }
}
