<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function admin(){
        return view('admin.index');
    }
    public function customers(){
        return view('admin.customers');

    }
    public function addCustomer(){
        return view('admin.addCustomer');

    }
    public function customerDetail(){
        return view('admin.customerDetail');

    }
    public function expenses(){
        return view('admin.expenses');

    }
    public function addExpense(){
        return view('admin.addExpense');

    }
    public function quotation(){
        return view('admin.quotation');

    }
}
