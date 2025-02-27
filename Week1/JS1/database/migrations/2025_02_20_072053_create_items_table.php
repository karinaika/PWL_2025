<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations. // menjalankan migrasi untuk membuat tabel 'items'.
     */
    public function up() //membuat fungsi 'up'
    {
        //untuk membuat tabel baru di database dimana menerima objek $table
        Schema::create('items', function (Blueprint $table) { 
            $table->id();                           //membuat kolom id (primary key)
            $table->string('name');         //kolom untuk menyimpan nama item
            $table->text('description');    //kolom untuk menyimpan deskripsi item
            $table->timestamps();                   //kolom created_at dan update_at
        });
    }

    /**
     * Reverse the migrations. //Menghapus tabel 'items' jika rollback dilakukan.
     */
    public function down(): void //membuat fungsi 'down'
    {
        //untuk menghapus tabel 'items'
        Schema::dropIfExists('items');
    }
};
