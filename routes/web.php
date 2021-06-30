<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\MpesaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('home', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');
//client side routes
Route::get('/', [IndexController::class, 'index']);

//admin routes
Route::get('admin', [AdminController::class, 'admin']);
Route::get('customers', [AdminController::class, 'customers']);
Route::get('addCustomer', [AdminController::class, 'addCustomer']);
Route::get('customerDetail', [AdminController::class, 'customerDetail']);
Route::get('expenses', [AdminController::class, 'expenses']);
Route::get('quotation', [AdminController::class, 'quotation']);
Route::get('addExpense', [AdminController::class, 'addExpense']);
Route::get('mpesa', [MpesaController::class, 'index']);
Route::get('cash', [CashController::class, 'index']);

require __DIR__.'/auth.php';
