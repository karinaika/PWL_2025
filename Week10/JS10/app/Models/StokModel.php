<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokModel extends Model
{
    protected $table = 't_stok';
    protected $primaryKey = 'stok_id';
    protected $fillable = [
        'barang_id',
        'user_id',
        'stok_tanggal',
        'stok_jumlah',
        'created_at',
        'updated_at'
    ];

    // Relasi dengan tabel Barang (jika ada model Barang)
    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id');
    }

    // Relasi dengan tabel User
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    //akses pakai $stok->jumlah
    public function getJumlahAttribute()
    {
        return $this->stok_jumlah;
    }
}