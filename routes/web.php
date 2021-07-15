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
Route::post('Login', [AuthController::class, 'login'])->name('Login');
Route::get('customers', [AdminController::class, 'customers']);
Route::get('addCustomer', [AdminController::class, 'addCustomer']);
Route::get('addProduct', [AdminController::class, 'addProduct']);
Route::get('addEmployee', [AdminController::class, 'addEmployee']);
Route::get('employees', [AdminController::class, 'employees']);
Route::get('addCash', [AdminController::class, 'addCash']);
Route::get('getUserInvoice', [AdminController::class, 'getUserInvoice']);
Route::get('products', [AdminController::class, 'product']);
Route::get('shop', [AdminController::class, 'shop']);
Route::get('cart', [IndexController::class, 'cart']);
Route::get('addByOne/{id}', [IndexController::class, 'addByOne']);
Route::get('cartReduceByOne/{id}', [IndexController::class, 'getReduceByOne']);
Route::get('cartRemove/{id}', [IndexController::class, 'removeItem']);
Route::get('checkout', [IndexController::class, 'checkout']);
Route::get('account', [IndexController::class, 'account']);
Route::post('storeCart', [IndexController::class, 'storeCart'])->name('storeCart');
Route::get('productDetail/{id}', [AdminController::class, 'productDetail']);
Route::post('storeProduct', [AdminController::class, 'storeProduct'])->name('storeProduct');
Route::post('makeCashPayment', [AdminController::class, 'makeCashPayment'])->name('makeCashPayment');
Route::post('storeEmployee', [AdminController::class, 'storeEmployee'])->name('storeEmployee');
Route::get('storeQuotation', [AdminController::class, 'storeQuotation']);
Route::get('storeCustomer', [AdminController::class, 'storeCustomer']);
Route::get('dueDate', [AdminController::class, 'dueDate']);
Route::get('updateDueDate', [AdminController::class, 'updateDueDate']);
Route::get('customerDetail', [AdminController::class, 'customerDetail']);
Route::get('expenses', [AdminController::class, 'expenses']);
Route::get('viewQuotation', [AdminController::class, 'viewQuotation']);
Route::get('quotes/{id}', [AdminController::class, 'quotes']);
Route::get('invoice', [AdminController::class, 'invoice']);
Route::get('quotation', [AdminController::class, 'quotation']);
Route::get('addExpense', [AdminController::class, 'addExpense']);
Route::get('mpesa', [MpesaController::class, 'index']);
Route::get('cash', [CashController::class, 'index']);
Route::get('test', [CashController::class, 'test']);
Route::post('testOne', [CashController::class, 'testOne']);

require __DIR__.'/auth.php';
