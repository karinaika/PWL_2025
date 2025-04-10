<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data_supplier = [
            [
                'supplier_kode' => 'SP001',
                'supplier_nama' => 'PT Sumber Jaya Abadi',
                'supplier_alamat' => 'Jl. Industri Raya No. 10, Jakarta Barat',
                'created_at' => Carbon::now()
            ],
            [
                'supplier_kode' => 'SP002',
                'supplier_nama' => 'PT Mitra Sejahtera',
                'supplier_alamat' => 'Jl. Pahlawan No. 25, Surabaya',
                'created_at' => Carbon::now()
            ],
            [
                'supplier_kode' => 'SP003',
                'supplier_nama' => 'PT Sentosa Makmur Bersama',
                'supplier_alamat' => 'Jl. Gatot Subroto No. 15, Bandung',
                'created_at' => Carbon::now()
            ],
        ];

        DB::table('m_supplier')->insert($data_supplier);
    }
}