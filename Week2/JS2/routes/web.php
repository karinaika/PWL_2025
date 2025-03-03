<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\PhotoController;

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

Route::get('/greeting', function () {
    return view('hello', ['name' => 'Karina']);
});

Route::get('/greeting', function () {
    return view('blog.hello', ['name' => 'Karina']);
});

Route::get('/greeting', [WelcomeController::class, 'greeting']);

// Route::get('/greeting', function () {
//     return view('hello', ['name' => 'Karina']);
// });

Route::resource('photos', PhotoController::class)->only([
    'index',
    'show'
]);

Route::resource('photos', PhotoController::class)->except([
    'create',
    'store',
    'update',
    'destroy'
]);

Route::resource('photos', PhotoController::class);

Route::get('/', HomeController::class);
Route::get('/about', AboutController::class);
Route::get('/articles/{id?}', ArticleController::class);

Route::get('/hello', [WelcomeController::class, 'hello']);

Route::get('/user/{name?}', function ($name = 'John') {
    return 'Nama saya ' . $name;
});

// Route::get('/user/{name?}', function ($name = null) {
//     return 'Nama saya ' . $name;
// });

Route::get('/articles/{id}', function ($Id) {
    return 'Halaman Artikel dengan ID : ' . $Id;
});

Route::get('/posts/{post}/comments/{comment}', function ($postId, $commentId) {
    return 'Pos ke-' . $postId . " Komentar ke-: " . $commentId;
});


Route::get('/user/{Karina}', function ($name) {
    return 'Nama Saya ' . $name;
});


Route::get('/about', function () {
    return 'NIM : 2341760042 Nama : Karina Ika Indasa';
});


Route::get('/', function () {
    return 'Selamat Datang';
});

Route::get('/world', function () { // Menentukan rute dengan metode GET ke URL "/world"
    return 'World'; // Saat URL "/world" diakses, teks "World" akan ditampilkan di browser
});

Route::get('/hello', function () { // Menentukan rute dengan metode GET ke URL "/hello"
    return 'Hello World'; // Saat URL "/hello" diakses, teks "Hello World" akan ditampilkan di browser
});

Route::get('/', function () {
    return view('welcome');
});
