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
//mpesa routes
Route::get('webhook', [MpesaController::class, 'webhook']);
Route::get('getWebhooks', [MpesaController::class, 'getWebhooks']);
Route::post('storeWebhooks', [MpesaController::class, 'storeWebhooks']);

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
Route::get('profile', [AdminController::class, 'profile']);
Route::post('editProfile/{id}', [AdminController::class, 'editProfile']);
Route::get('addCustomer', [AdminController::class, 'addCustomer']);
Route::post('filterInvoice', [AdminController::class, 'filterInvoice']);
Route::get('addProduct', [AdminController::class, 'addProduct']);
Route::get('getReceipt/{id}', [AdminController::class, 'getReceipt']);
Route::get('addEmployee', [AdminController::class, 'addEmployee']);
Route::get('employees', [AdminController::class, 'employees']);
Route::get('addCash', [AdminController::class, 'addCash']);
Route::get('bill', [AdminController::class, 'bill']);
Route::get('getQProducts', [AdminController::class, 'getQProducts']);
Route::post('billing', [AdminController::class, 'billing'])->name('billing');
Route::post('billingEach', [AdminController::class, 'billingEach'])->name('billingEach');
Route::post('editQProduct', [AdminController::class, 'editQProduct'])->name('editQProduct');
Route::get('deletePro', [AdminController::class, 'deletePro']);
Route::get('receipt/{id}', [AdminController::class, 'receipt']);
Route::get('pdf', [AdminController::class, 'pdf']);
Route::get('ttt', [AdminController::class, 'ttt']);
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
Route::post('updateInvoiceDueDate', [AdminController::class, 'updateInvoiceDueDate']);
Route::get('getInvoiceId', [AdminController::class, 'getInvoiceId']);
Route::get('getInvoice', [AdminController::class, 'getInvoice']);
Route::get('invoicePayment/{id}', [AdminController::class, 'invoicePayment']);
Route::get('customerDetail/{id}', [AdminController::class, 'customerDetail']);
Route::get('downloadPdf', [AdminController::class, 'downloadPdf']);
Route::get('expenses', [AdminController::class, 'expenses']);
Route::get('viewQuotation', [AdminController::class, 'viewQuotation']);
Route::get('allQuotes', [AdminController::class, 'allQuotes']);
Route::get('expiredQuotes', [AdminController::class, 'expiredQuotes']);
Route::get('viewInvoice', [AdminController::class, 'viewInvoice']);
Route::get('currentDate', [AdminController::class, 'currentDate']);
Route::get('currentDat', [AdminController::class, 'currentDat']);
Route::get('allInvoices', [AdminController::class, 'allInvoices']);
Route::get('quotes/{id}', [AdminController::class, 'quotes']);
Route::post('invoice/{id}', [AdminController::class, 'invoice']);
Route::get('printInvoice/{id}', [AdminController::class, 'printInvoice']);
Route::get('quotation', [AdminController::class, 'quotation']);
Route::get('addExpense', [AdminController::class, 'addExpense']);
Route::get('mpesa', [MpesaController::class, 'index']);
Route::get('cash', [CashController::class, 'index']);
Route::get('test', [CashController::class, 'test']);
Route::post('testOne', [CashController::class, 'testOne']);

require __DIR__.'/auth.php';
