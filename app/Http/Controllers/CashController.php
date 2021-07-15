<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CashController extends Controller
{
    public function index(){
        $cashs = Cash::all();
        return view('admin.cash',[
            'cashs'=>$cashs
        ]);
    }
    public function test(){
        $posts = Http::get("https://test.synatechafrica.com/clients");
        return $posts;
    }
    public function testOne(){
        $posts = Http::post("https://test.synatechafrica.com/clients");
        return $posts;
    }
}
