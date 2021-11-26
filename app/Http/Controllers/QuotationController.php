<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function createInvoice(){
        return view('admin.createInvoice');
    }
}
