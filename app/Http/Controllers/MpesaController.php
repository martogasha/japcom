<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MpesaController extends Controller
{
    public function index(){
        return view('admin.mpesa');
    }
}
