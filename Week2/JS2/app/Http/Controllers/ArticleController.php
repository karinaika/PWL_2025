<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __invoke($id = null)
    {
        return 'Halaman Artikel dengan Id ' . $id;
    }
}
