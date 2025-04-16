<?php

namespace App\Http\Controllers;

use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use App\Models\BarangModel;
use App\Models\UserModel;
use App\Models\StokModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Transaksi Penjualan',
            'list' => ['Home', 'Penjualan'],
        ];

        $page = (object) [
            'title' => 'Daftar transaksi penjualan yang terdaftar dalam sistem',
        ];

        $activeMenu = 'penjualan';

        $kasir = UserModel::all();

        return view('penjualan.index', [
            'breadcrumb' => $breadcrumb,
            'activeMenu' => $activeMenu,
            'page' => $page,
            'kasir' => $kasir
        ]);
    }

    public function list(Request $request)
    {
        $penjualans = PenjualanModel::select('penjualan_id', 'penjualan_kode', 'pembeli', 'penjualan_tanggal', 'user_id')
            ->with(['user']);

        if ($request->user_id) {
            $penjualans->where('user_id', $request->user_id);
        }

        return DataTables::of($penjualans)
            ->addIndexColumn()
            ->addColumn('total', function ($penjualan) {
                $total = PenjualanDetailModel::where('penjualan_id', $penjualan->penjualan_id)
                    ->sum(DB::raw('harga * jumlah'));
                return 'Rp ' . number_format($total, 0, ',', '.');
            })
            ->addColumn('nama', function ($penjualan) {
                return $penjualan->user ? $penjualan->user->nama : '-';
            })
            ->addColumn('aksi', function ($penjualan) {
                $btn = '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function import()
    {
        return view('penjualan.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_penjualan' => ['required', 'mimes:xlsx', 'max:1024'],
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            try {
                $file = $request->file('file_penjualan');
                $reader = IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray(null, false, true, true);

                $penjualans = [];
                $details = [];
                $currentPenjualanKode = null;

                if (count($data) > 1) {
                    foreach ($data as $baris => $value) {
                        if ($baris <= 1) {
                            continue; // Lewati header
                        }

                        if (empty($value['B']) && empty($value['D']) && empty($value['E'])) {
                            continue; // Lewati baris kosong
                        }

                        if (!empty($value['B'])) {
                            if (empty($value['A']) || !UserModel::find($value['A'])) {
                                continue;
                            }

                            if (PenjualanModel::where('penjualan_kode', $value['B'])->exists()) {
                                continue;
                            }

                            if (empty($value['C'])) {
                                continue;
                            }

                            $tanggal = now(); //SIMPAN dengan format Y-m-d H:i:s (default Laravel/MySQL)

                            $penjualan = [
                                'user_id' => $value['A'],
                                'penjualan_kode' => $value['B'],
                                'pembeli' => $value['C'],
                                'penjualan_tanggal' => $tanggal,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];

                            $currentPenjualanKode = $value['B'];
                            $penjualans[] = $penjualan;
                        }

                        if (!empty($value['E']) && !empty($value['E'])) {
                            $barang = BarangModel::find($value['D']);
                            if (!$barang) {
                                continue;
                            }

                            if (!is_numeric($value['E']) || $value['E'] <= 0) {
                                continue;
                            }

                            if (!$currentPenjualanKode) {
                                continue;
                            }

                            $details[] = [
                                'penjualan_kode' => $currentPenjualanKode,
                                'barang_id' => $value['D'],
                                'harga' => $barang->harga_jual,
                                'jumlah' => (int) $value['E'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                    if (count($penjualans) > 0 || count($details) > 0) {
                        DB::beginTransaction();
                        try {
                            if (count($penjualans) > 0) {
                                PenjualanModel::insertOrIgnore($penjualans);
                            }

                            if (count($details) > 0) {
                                $penjualanIds = PenjualanModel::whereIn('penjualan_kode', array_column($details, 'penjualan_kode'))
                                    ->pluck('penjualan_id', 'penjualan_kode');

                                $detailsToInsert = [];
                                foreach ($details as $detail) {
                                    if (isset($penjualanIds[$detail['penjualan_kode']])) {
                                        $detailsToInsert[] = [
                                            'penjualan_id' => $penjualanIds[$detail['penjualan_kode']],
                                            'barang_id' => $detail['barang_id'],
                                            'harga' => $detail['harga'],
                                            'jumlah' => $detail['jumlah'],
                                            'created_at' => $detail['created_at'],
                                            'updated_at' => $detail['updated_at'],
                                        ];
                                    }
                                }

                                if (count($detailsToInsert) > 0) {
                                    PenjualanDetailModel::insertOrIgnore($detailsToInsert);
                                }
                            }

                            DB::commit();
                            return response()->json([
                                'status' => true,
                                'message' => 'Data berhasil diimport',
                            ]);
                        } catch (\Exception $e) {
                            DB::rollBack();
                            Log::error('Impor gagal: ' . $e->getMessage());
                            return response()->json([
                                'status' => false,
                                'message' => 'Terjadi kesalahan saat mengimpor: ' . $e->getMessage(),
                            ]);
                        }
                    }

                    return response()->json([
                        'status' => false,
                        'message' => 'Tidak ada data yang valid untuk diimport',
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Impor gagal: ' . $e->getMessage());
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat mengimpor: ' . $e->getMessage(),
                ]);
            }
        }

        return redirect('/');
    }

    public function export_excel()
    {
        $penjualans = PenjualanModel::select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
            ->with(['user'])
            ->orderBy('penjualan_id')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'ID Penjualan');
        $sheet->setCellValue('C1', 'Kode Penjualan');
        $sheet->setCellValue('D1', 'Pembeli');
        $sheet->setCellValue('E1', 'Tanggal');
        $sheet->setCellValue('F1', 'Petugas');
        $sheet->setCellValue('G1', 'Barang');
        $sheet->setCellValue('H1', 'Jumlah');
        $sheet->setCellValue('I1', 'Harga');
        $sheet->setCellValue('J1', 'Subtotal');
        $sheet->setCellValue('K1', 'Total');

        $sheet->getStyle('A1:K1')->getFont()->setBold(true);

        $no = 1;
        $baris = 2;
        foreach ($penjualans as $penjualan) {
            $total = PenjualanDetailModel::where('penjualan_id', $penjualan->penjualan_id)
                ->sum(DB::raw('harga * jumlah'));

            // Fill main penjualan data
            $sheet->setCellValue('A' . $baris, $no);
            $sheet->setCellValue('B' . $baris, $penjualan->penjualan_id);
            $sheet->setCellValue('C' . $baris, $penjualan->penjualan_kode);
            $sheet->setCellValue('D' . $baris, $penjualan->pembeli);
            $sheet->setCellValue('E' . $baris, $penjualan->penjualan_tanggal);
            $sheet->setCellValue('F' . $baris, $penjualan->user ? $penjualan->user->nama : '-');
            $sheet->setCellValue('K' . $baris, $total);

            // Add product details for this sale
            $detailBaris = $baris;
            $details = PenjualanDetailModel::where('penjualan_id', $penjualan->penjualan_id)->get();

            foreach ($details as $detail) {
                $namaBarang = $detail->barang->barang_nama ?? '-';
                $jumlah = $detail->jumlah;
                $harga = $detail->harga;
                $subtotal = $jumlah * $harga;

                // Fill product details for each row
                $sheet->setCellValue('G' . $detailBaris, $namaBarang);
                $sheet->setCellValue('H' . $detailBaris, $jumlah);
                $sheet->setCellValue('I' . $detailBaris, $harga);
                $sheet->setCellValue('J' . $detailBaris, $subtotal);

                // Move to next row for each item
                $detailBaris++;
            }

            // Move to next row for the next sale
            $baris = $detailBaris;
            $no++;
        }

        // Auto-size columns
        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set sheet title
        $sheet->setTitle('Data Penjualan');

        // Write to file
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data_Penjualan_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    }


    public function export_pdf()
    {
        // Ambil data penjualan beserta detail barangnya
        $penjualans = PenjualanModel::select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal')
            ->with(['user', 'details', 'details.barang'])  // Mengambil detail barang
            ->orderBy('penjualan_id')
            ->get();

        // Kirim data penjualans ke tampilan PDF
        $pdf = Pdf::loadView('penjualan.export_pdf', ['penjualans' => $penjualans]);

        // Set ukuran kertas dan orientasi
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        // Stream file PDF
        return $pdf->stream('Data Penjualan ' . date('Y-m-d H:i:s') . '.pdf');
    }

    // Menyimpan data penjualan baru dgn ajax
    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'penjualan_kode' => 'required|string|min:3|unique:t_penjualan,penjualan_kode',
                'pembeli' => 'required|string|min:3',
                'penjualan_tanggal' => 'required|date',
                'user_id' => 'required|integer|exists:m_user,user_id',
                'barang_id' => 'required|array',
                'barang_id.*' => 'required|integer|exists:m_barang,barang_id',
                'jumlah' => 'required|array',
                'jumlah.*' => 'required|integer|min:1',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            try {
                // Gunakan transaksi database untuk memastikan konsistensi
                return DB::transaction(function () use ($request) {
                    // Simpan header transaksi
                    $penjualan = new PenjualanModel();
                    $penjualan->penjualan_kode = $request->penjualan_kode;
                    $penjualan->pembeli = $request->pembeli;
                    $penjualan->penjualan_tanggal = $request->penjualan_tanggal;
                    $penjualan->user_id = $request->user_id;
                    $penjualan->save();

                    // Simpan detail transaksi dan validasi stok
                    foreach ($request->barang_id as $index => $barang_id) {
                        $barang = BarangModel::find($barang_id);
                        $stok = StokModel::where('barang_id', $barang_id)->first();

                        // Validasi stok
                        if (!$stok || $stok->stok_jumlah < $request->jumlah[$index]) {
                            throw new \Exception("Stok barang {$barang->barang_nama} tidak cukup!");
                        }

                        // Simpan detail
                        $detail = new PenjualanDetailModel();
                        $detail->penjualan_id = $penjualan->penjualan_id;
                        $detail->barang_id = $barang_id;
                        $detail->harga = $barang->harga_jual;
                        $detail->jumlah = $request->jumlah[$index];
                        $detail->save();

                        // Kurangi stok
                        $stok->stok_jumlah -= $request->jumlah[$index];
                        $stok->save();
                    }

                    return response()->json([
                        'status' => true,
                        'message' => 'Data penjualan berhasil disimpan',
                    ]);
                });
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
                ]);
            }
        }
        return redirect('/');
    }


    // Mengedit data penjualan dgn ajax
    public function edit_ajax(string $id)
    {
        $penjualan = PenjualanModel::with('details')->find($id);
        $user = UserModel::select('user_id', 'nama')->get();
        $barangs = BarangModel::all();

        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan tidak ditemukan',
            ]);
        }

        return view('penjualan.edit_ajax', ['penjualan' => $penjualan, 'user' => $user, 'barangs' => $barangs]);
    }

    public function update_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'penjualan_kode' => 'required|string|min:3|unique:t_penjualan,penjualan_kode,' . $id . ',penjualan_id',
                'user_id' => 'required|integer|exists:m_user,user_id',
                'pembeli' => 'required|string|min:3',
                'penjualan_tanggal' => 'required|date',
                'barang_id' => 'required|array',
                'barang_id.*' => 'required|integer|exists:m_barang,barang_id',
                'jumlah' => 'required|array',
                'jumlah.*' => 'required|integer|min:1',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            try {
                return DB::transaction(function () use ($request, $id) {
                    $penjualan = PenjualanModel::find($id);
                    if (!$penjualan) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Data penjualan tidak ditemukan',
                        ]);
                    }

                    // Kembalikan stok sebelumnya
                    $oldDetails = PenjualanDetailModel::where('penjualan_id', $id)->get();
                    foreach ($oldDetails as $detail) {
                        $stok = StokModel::where('barang_id', $detail->barang_id)->first();
                        if ($stok) {
                            $stok->stok_jumlah += $detail->jumlah;
                            $stok->save();
                        }
                    }

                    PenjualanDetailModel::where('penjualan_id', $id)->delete();

                    $penjualan->update($request->all());

                    foreach ($request->barang_id as $index => $barang_id) {
                        $barang = BarangModel::find($barang_id);
                        $stok = StokModel::where('barang_id', $barang_id)->first();

                        if (!$stok || $stok->stok_jumlah < $request->jumlah[$index]) {
                            throw new \Exception("Stok barang {$barang->barang_nama} tidak cukup!");
                        }

                        $detail = new PenjualanDetailModel();
                        $detail->penjualan_id = $penjualan->penjualan_id;
                        $detail->barang_id = $barang_id;
                        $detail->harga = $barang->harga_jual;
                        $detail->jumlah = $request->jumlah[$index];
                        $detail->save();

                        $stok->stok_jumlah -= $request->jumlah[$index];
                        $stok->save();
                    }

                    return response()->json([
                        'status' => true,
                        'message' => 'Data penjualan berhasil diubah',
                    ]);
                });
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal mengubah data: ' . $e->getMessage(),
                ]);
            }
        }
        return redirect('/');
    }

    public function confirm_ajax(string $id)
    {
        $penjualan = PenjualanModel::find($id);
        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan tidak ditemukan',
            ]);
        }
        return view('penjualan.confirm_ajax', ['penjualan' => $penjualan]);
    }

    public function delete_ajax(Request $request, string $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return DB::transaction(function () use ($id) {
                $penjualan = PenjualanModel::find($id);
                if ($penjualan) {
                    $details = PenjualanDetailModel::where('penjualan_id', $id)->get();
                    foreach ($details as $detail) {
                        $stok = StokModel::where('barang_id', $detail->barang_id)->first();
                        if ($stok) {
                            $stok->stok_jumlah += $detail->jumlah;
                            $stok->save();
                        }
                    }

                    PenjualanDetailModel::where('penjualan_id', $id)->delete();
                    $penjualan->delete();

                    return response()->json([
                        'status' => true,
                        'message' => 'Data penjualan berhasil dihapus',
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data penjualan tidak ditemukan',
                    ]);
                }
            });
        }
        return redirect('/');
    }

    public function show_ajax(string $id)
    {
        $penjualan = PenjualanModel::with('user', 'details.barang')->find($id);
        if (!$penjualan) {
            return response()->json([
                'status' => false,
                'message' => 'Data penjualan tidak ditemukan',
            ]);
        }
        return view('penjualan.show_ajax', ['penjualan' => $penjualan]);
    }


    // Membuat dan menampilkan halaman form tambah penjualan dgn Ajax
    public function create_ajax()
    {
        $user = UserModel::all();
        $barangs = BarangModel::all(); // Ambil data barang untuk form
        return view('penjualan.create_ajax', compact('user', 'barangs'));
    }

}