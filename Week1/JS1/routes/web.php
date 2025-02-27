<?php

use App\Http\Controllers\ItemController;    //Mengimpor ItemController agar bisa digunakan dalam routing
use Illuminate\Support\Facades\Route;       //Mengimpor kelas Route untuk mendefinisikan rute aplikasi

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () { // Mendefinisikan rute untuk halaman utama ("/")
    return view('welcome'); // Menampilkan view "welcome" saat mengakses halaman utama
});

Route::resource('items', ItemController::class); // Membuat resource route untuk 'items', yang secara otomatis menyediakan semua rute CRUD (Create, Read, Update, Delete) untuk ItemController