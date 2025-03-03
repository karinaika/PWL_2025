<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PageController extends Controller
{
    public function index(){
        return('Selamat Datang');
    }

    public function about(){
        return('Karina Ika Indasa (2341760042)');
    }

    public function articles($id = null)
    {
        return 'Halaman Artikel dengan Id ' . $id;
    }
}
