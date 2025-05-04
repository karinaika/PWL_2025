<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\UserModel;
use App\Models\DetailPenjualanModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable; 

class PenjualanModel extends Model
{

    public function getJWTIdentifier(){ 
        return $this->getKey();
    }
    
    public function getJWTCustomClaims(){ 
        return [];
    }

    use HasFactory;
    protected $table = 't_penjualan';
    protected $primaryKey = 'penjualan_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal', 'image'];

    //Relasi ke tabel user
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PenjualanDetailModel::class, 'penjualan_id', 'penjualan_id');}


     // Menambahkan method untuk mendapatkan nama
     public function getUsernameAttribute()
     {
         return $this->user->nama ?? null;  // Ambil nama dari relasi user
     }

     protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => $image ? url('/storage/transaksi/' . $image) : null
        );
    }
}