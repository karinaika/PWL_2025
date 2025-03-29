<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kategori_id' => 1, 'kategori_kode' => 'FB', 'kategori_nama' => 'Food & Beverage'],
            ['kategori_id' => 2, 'kategori_kode' => 'BH', 'kategori_nama' => 'Beauty & Health'],
            ['kategori_id' => 3, 'kategori_kode' => 'HC', 'kategori_nama' => 'Home Care'],
            ['kategori_id' => 4, 'kategori_kode' => 'BK', 'kategori_nama' => 'Baby & Kids'],
            ['kategori_id' => 5, 'kategori_kode' => 'EL', 'kategori_nama' => 'Electronics'],
        ];

        DB::table('m_kategori')->insert($data);
    }
}
