<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokModel extends Model
{
    use HasFactory;

    protected $table = 't_stok'; // <- WAJIB karena nama tabel kamu bukan plural
    protected $primaryKey = 'stok_id'; // <- sesuaikan dengan nama primary key

    protected $fillable = [
        'barang_id',
        'stok_jumlah',
        'stok_tanggal',
        'user_id'
    ];

    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id', 'barang_id');
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}
