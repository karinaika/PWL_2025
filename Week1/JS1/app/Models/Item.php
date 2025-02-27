<?php

namespace App\Models; // Mendeklarasikan namespace model 

use Illuminate\Database\Eloquent\Factories\HasFactory; // Menggunakan trait HasFactory untuk pembuatan data dummy
use Illuminate\Database\Eloquent\Model; // Menggunakan kelas Model sebagai dasar dari model ini

class Item extends Model // kelas Item yang mewarisi Model
{
    use HasFactory; // Menggunakan trait HasFactory untuk mendukung factory dalam pengujian dan seeding

    protected $fillable = ['name', 'description']; // Atribut yang bisa diisi 
}