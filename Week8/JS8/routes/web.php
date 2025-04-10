<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::pattern('id', '[0-9]+'); // Pastikan parameter {id} hanya berupa angka

// Rute otentikasi
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'postRegister']);

// Semua rute di bawah ini hanya bisa diakses jika sudah login
Route::middleware(['auth'])->group(function () {
    //masukkan semua route yang perlu autentikasi di sini
    Route::get('/', [WelcomeController::class, 'index']);

    Route::middleware(['authorize:ADM'])->group(function () {
        Route::group(['prefix' => 'user'], function () {
            Route::get('/', [UserController::class, 'index']); // menampilkan halaman awal user
            Route::post('/list', [UserController::class, 'list']); // menampilkan data user dalam bentuk json untuk datatables
            Route::get('/create', [UserController::class, 'create']); // menampilkan halaman form tambah user
            Route::post('/', [UserController::class, 'store']); // menyimpan data user baru
            //Create Menggunakan AJAX
            Route::get('/create_ajax', [UserController::class, 'create_ajax']); // Menampilkan halaman form tambah user Ajax
            Route::post('/ajax', [UserController::class, 'store_ajax']); // Menyimpan data user baru Ajax
            Route::get('/{id}', [UserController::class, 'show']); // menampilkan detail user
            Route::get('/{id}/edit', [UserController::class, 'edit']); // menampilkan halaman form edit user
            Route::put('/{id}', [UserController::class, 'update']); // menyimpan perubahan data user
            //Edit Menggunakan AJAX
            Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']); // Menampilkan halaman form edit user Ajax
            Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']); // Menyimpan perubahan data user Ajax
            //Delete Menggunakan AJAX
            Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']); // Untuk tampilkan form confirm delete user Ajax
            Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']); // Untuk hapus data user Ajax
            Route::delete('/{id}', [UserController::class, 'destroy']); // menghapus data user
            // Import User with Excel
            Route::get('import', [UserController::class, 'import']); // ajax form upload excel
            Route::post('import_ajax', [UserController::class, 'import_ajax']); // ajax import excel
        });
    });

    //artinya semua route di dalam group ini harus punya role ADM (Administrator)
    Route::middleware(['authorize:ADM'])->group(function () {
        Route::group(['prefix' => 'level'], function () {
            Route::get('/', [LevelController::class, 'index']); // menampilkan halaman awal level
            Route::post("/list", [LevelController::class, 'list']); // menampilkan data level dalam bentuk json untuk datatables
            Route::get('/create', [LevelController::class, 'create']); // menampilkan halaman form tambah level
            Route::post('/', [LevelController::class, 'store']); // menyimpan data level baru
            //Create Menggunakan AJAX
            Route::get('/create_ajax', [LevelController::class, 'create_ajax']); // Menampilkan halaman form tambah level Ajax
            Route::post('/ajax', [LevelController::class, 'store_ajax']); // Menyimpan data level baru Ajax
            Route::get('/{id}', [LevelController::class, 'show']); // menampilkan detail level
            Route::get('/{id}/edit', [LevelController::class, 'edit']); // menampilkan halaman form edit level
            Route::put('/{id}', [LevelController::class, 'update']); // menyimpan perubahan data level
            //Edit Menggunakan AJAX
            Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']); // Menampilkan halaman form edit level Ajax
            Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax']); // Menyimpan perubahan data level Ajax
            //Delete Menggunakan AJAX
            Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']); // Untuk tampilkan form confirm delete level Ajax
            Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']); // Untuk hapus data level Ajax
            Route::delete('/{id}', [LevelController::class, 'destroy']); // menghapus data level
            // Import Level with Excel
            Route::get('import', [LevelController::class, 'import']); // ajax form upload excel
            Route::post('import_ajax', [LevelController::class, 'import_ajax']); // ajax import excel
        });
    });

    // artinya semua route di dalam group ini harus punya role ADM (Administrator) dan MNG (Manager)
    Route::middleware(['authorize:ADM,MNG'])->group(function () {
        Route::group(['prefix' => 'kategori'], function () {
            Route::get('/', [KategoriController::class, 'index']); // menampilkan halaman awal kategori
            Route::post("/list", [KategoriController::class, 'list']); // menampilkan data kategori dalam bentuk json untuk datatables
            Route::get('/create', [KategoriController::class, 'create']); // menampilkan halaman form tambah kategori
            Route::post('/', [KategoriController::class, 'store']); // menyimpan data kategori baru 
            // Create menggunakan AJAX
            Route::get('/create_ajax', [KategoriController::class, 'create_ajax']); // menampilkan halaman form tambah kategori ajax
            Route::post('/ajax', [KategoriController::class, 'store_ajax']); // menyimpan data kategori baru ajax
            Route::get('/{id}', [KategoriController::class, 'show']); // menampilkan detail kategori
            Route::get('/{id}/edit', [KategoriController::class, 'edit']); // menampilkan halaman form edit kategori
            Route::put('/{id}', [KategoriController::class, 'update']); // menyimpan perubahan data kategori
            // Edit menggunakan AJAX
            Route::get('/{id}/edit_ajax', [KategoriController::class, 'edit_ajax']); // menampilkan halaman form edit kategori ajax
            Route::put('/{id}/update_ajax', [KategoriController::class, 'update_ajax']); // menyimpan perubahan data kategori ajax
            // Delete menggunakan AJAX
            Route::get('/{id}/delete_ajax', [KategoriController::class, 'confirm_ajax']); //menampilkan form confirm delete kategori ajax
            Route::delete('/{id}/delete_ajax', [KategoriController::class, 'delete_ajax']); // menghapus data kategori ajax
            Route::delete('/{id}', [KategoriController::class, 'destroy']); // menghapus data kategori
            // Import Kategori with Excel
            Route::get('import', [KategoriController::class, 'import']); // ajax form upload excel
            Route::post('import_ajax', [KategoriController::class, 'import_ajax']); // ajax import excel
        });
    });

    // Staff hanya bisa melihat data barang
    Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
        Route::group(['prefix' => 'barang'], function () {
            Route::get('/', [BarangController::class, 'index']); // Menampilkan daftar barang
            Route::post('/list', [BarangController::class, 'list']); // Menampilkan data barang dalam bentuk JSON untuk datatables
            Route::get('/{id}', [BarangController::class, 'show']); // Menampilkan detail barang
        });
    });

    // ADM & MNG bisa menambah, mengedit, dan menghapus barang
    Route::middleware(['authorize:ADM,MNG'])->group(function () {
        Route::group(['prefix' => 'barang'], function () {
            Route::get('/create', [BarangController::class, 'create']); // Form tambah barang
            Route::post('/', [BarangController::class, 'store']); // Simpan barang baru
            // Create menggunakan AJAX
            Route::get('/create_ajax', [BarangController::class, 'create_ajax']); // Form tambah barang AJAX
            Route::post('/ajax', [BarangController::class, 'store_ajax']); // Simpan barang baru AJAX
            Route::get('/{id}/edit', [BarangController::class, 'edit']); // Form edit barang
            Route::put('/{id}', [BarangController::class, 'update']); // Simpan perubahan barang
            // Edit menggunakan AJAX
            Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax']); // Form edit barang AJAX
            Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax']); // Simpan perubahan barang AJAX
            // Delete menggunakan AJAX
            Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']); // Form konfirmasi hapus barang AJAX
            Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']); // Hapus barang AJAX
            Route::delete('/{id}', [BarangController::class, 'destroy']); // Hapus barang
            // Import Barang with Excel
            Route::get('import', [BarangController::class, 'import']); // ajax form upload excel
            Route::post('import_ajax', [BarangController::class, 'import_ajax']); // ajax import excel
        });
    });

    Route::group(['prefix' => 'supplier'], function () {
        Route::get('/', [SupplierController::class, 'index']);
        Route::post('/list', [SupplierController::class, 'list']);
        Route::get('/create', [SupplierController::class, 'create']);
        Route::post('/', [SupplierController::class, 'store']);
        // Create menggunakan AJAX
        Route::get('/create_ajax', [SupplierController::class, 'create_ajax']); // menampilkan halaman form tambah Supplier ajax
        Route::post('/ajax', [SupplierController::class, 'store_ajax']); // menyimpan data Supplier baru ajax
        Route::get('/{id}', [SupplierController::class, 'show']);
        Route::get('/{id}/edit', [SupplierController::class, 'edit']);
        Route::put('/{id}', [SupplierController::class, 'update']);
        // Edit menggunakan AJAX
        Route::get('/{id}/edit_ajax', [SupplierController::class, 'edit_ajax']); // menampilkan halaman form edit Supplier ajax
        Route::put('/{id}/update_ajax', [SupplierController::class, 'update_ajax']); // menyimpan perubahan data Supplier ajax
        // Delete menggunakan AJAX
        Route::get('/{id}/delete_ajax', [SupplierController::class, 'confirm_ajax']); //menampilkan form confirm delete Supplier ajax
        Route::delete('/{id}/delete_ajax', [SupplierController::class, 'delete_ajax']); // menghapus data Supplier ajax
        Route::delete('/{id}', [SupplierController::class, 'destroy']);
    });

    // // artinya semua route di dalam group ini harus punya role ADM (Administrator) dan MNG (Manager)
    // Route::middleware(['authorize:ADM,MNG'])->group(function(){
    //     Route::get('/barang', [BarangController::class, 'index']);
    //     Route::post('/barang/list', [BarangController::class, 'list']);
    //     Route::get('/barang/create_ajax', [BarangController::class, 'create_ajax']); // ajax form create
    //     Route::post('/barang_ajax', [BarangController::class, 'store_ajax']); // ajax store
    //     Route::get('/barang/{id}/edit_ajax', [BarangController::class, 'edit_ajax']); // ajax form edit
    //     Route::put('/barang/{id}/update_ajax', [BarangController::class, 'update_ajax']); // ajax update
    //     Route::get('/barang/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']); // ajax form confirm
    //     Route::delete('/barang/{id}/delete_ajax', [BarangController::class, 'delete_ajax']); // ajax delete
    // });
});