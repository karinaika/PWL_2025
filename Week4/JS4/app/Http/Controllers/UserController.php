<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = UserModel::findOr(20, ['username', 'nama'], function() {
            abort(404);
        });
        return view('user', ['data' => $user]);
        // $user = UserModel::findOr(1, ['username', 'nama'], function() {
        //     abort(404);
        // });
        //$user = UserModel::firstWhere('level_id', 1);
        //$user = UserModel::where('level_id', 1)->first();
        //$user = UserModel::find(1); //untuk mencari data dengan user_id = 1.

        // // tambah data user dengan Eloquent Model
        // $data = [
        //     'level_id' => 2, 
        //     'username' => 'manager_tiga',  //'username' => 'manager_dua'
        //     'nama' => 'Manager 3',
        //     'password' => Hash::make('12345')
        // ];
        // UserModel::create($data);

        // // akses model UserModel
        // $user = UserModel::all(); // ambil semua data dari tabel m_user 

        // return view('user', ['data' => $user]);
    }
}