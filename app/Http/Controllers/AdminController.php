<?php

namespace App\Http\Controllers;

use App\Cat;
use App\Models\Cash;
use App\Models\Inv;
use App\Models\Invoice;
use App\Models\Mpesa;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Qproduct;
use App\Models\Quotation;
use App\Models\User;
use Faker\Provider\DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Session;
use Dompdf\Dompdf;
use function GuzzleHttp\Promise\all;


class AdminController extends Controller
{
    public function admin(){
        if (Auth::check()) {
            if (Auth::user()->role==0 || Auth::user()->role==1) {
                return view('admin.index');
            }
        }
        else{
            return redirect(url('login'));
        }
    }
    public function profile(){
        if (Auth::check()) {
            if (Auth::user()->role==0 || Auth::user()->role==1) {
                return view('admin.profile');
            }
        }
        else{
            return redirect(url('login'));

        }
    }
    public function editProfile(Request $request , $id){
        $edit = User::find($id);
        $edit->first_name = $request->first_name;
        $edit->last_name = $request->last_name;
        $edit->phone = $request->phone;
        $edit->email = $request->email;
        $edit->password = Hash::make($request->password);
        $edit->save();
        return redirect()->back()->with('success','profile updated success');
    }
    public function customers(){
        $customers = User::where('bandwidth','!=', null)->where('location','!=',null)->get();
        return view('admin.customers',[
            'customers'=>$customers,
        ]);
    }
    public function product(){
        $products = Product::all();
        return view('admin.products',[
            'products'=>$products
        ]);
    }
    public function addEmployee(){
        return view('admin.addEmployee');
    }
    public function shop(){
        $oldCart = Session::get('cat');
        $cart = new Cat($oldCart);
        $shops = Product::all();
        return view('admin.shop',[
            'shops'=>$shops,
            'products'=>$cart->item,
            'totalPrice'=>$cart->totalPrice
        ]);
    }
    public function productDetail($id){
        $productDetail = Product::find($id);
        return view('admin.productDetail',[
            'productDetail'=>$productDetail
        ]);
    }
    public function storeQuotation(Request $request){
        if ($request->ajax()){
            $output = "";
        }
        $check = Quotation::where('status',0)->first();
        if ($check==null){
            $store = Quotation::create([
                'name'=>$request->customer_name,
                'estimate_date'=>$request->estimated_date,
                'expiry_date'=>$request->expiry_date,
                'status'=>0,
                'statas'=>0,
            ]);
        }
        $quotation = Quotation::where('status',0)->first();
        $store = Qproduct::create([
           'name'=>$request->product_name,
           'quantity'=>$request->quantity,
           'amount'=>$request->amount,
           'total'=>$request->amount*$request->quantity,
           'quotation_id'=>$quotation->id,
        ]);
        return redirect()->back()->with('success','saved Success');
    }
    public function storeEmployee(Request $request){
        $store = User::create([
           'first_name'=>$request->first_name,
           'last_name'=>$request->last_name,
           'phone'=>$request->phone,
           'email'=>$request->email,
           'role'=>$request->role,
           'password'=>Hash::make('password'),
        ]);
        return redirect()->back()->with('success','EMPLOYEE ADDED SUCCESSFULLY');
    }
    public function employees(){
        $customers = User::all();
        return view('admin.employee',[
            'customers'=>$customers
        ]);
    }
    public function addProduct(){
        return view('admin.addProduct');

    }
    public function storeProduct(Request $request){
        $store = new Product();
        $store->name = $request->input('name');
        $store->desc = $request->input('desc');
        $store->amount = $request->input('amount');
        if ($request->photo) {
            $file = $request->file('photo');
            $extension = $file->getClientOriginalName();
            $filename = time() . '.' . $extension;
            $file->move('uploads/product/', $filename);
            $store->photo = $filename;
        }
        $store->save();
        return redirect()->back()->with('success','PRODUCT SAVED');
    }
    public function addCustomer(){
        return view('admin.addCustomer');

    }
    public function addCash(){
        $users = User::where('role',2)->get();
        return view('admin.addCash',[
            'users'=>$users
        ]);

    }
    public function bill(){
        $users = User::where('role',2)->get();
        return view('admin.bill',[
            'users'=>$users
        ]);

    }
    public function deletePro(Request $request){
        if ($request->ajax()){
            $delete = Qproduct::find($request->id);
            $delete->delete();
        }
    }
    public function editQProduct(Request $request){
        $edit = Qproduct::find($request->id);
        $edit->name = $request->name;
        $edit->quantity = $request->quantity;
        $edit->amount = $request->amount;
        $edit->save();
        return redirect()->back()->with('success','Updated');
    }
    public function getQProducts(Request $request){
        if ($request->ajax()){
            $output = "";
            $get = Qproduct::find($request->id);
            $output = '
                    <input type="hidden" value="'.$get->id.'" name="id">
                      <div class="col-lg-12 col-12 form-group">
                    <label>Name</label>
                    <input type="text" value="'.$get->name.'" class="form-control" name="name">
                </div>
                <div class="col-lg-12 col-12 form-group">
                    <label>Quantity</label>
                    <input type="text" value="'.$get->quantity.'" class="form-control" name="quantity">
                </div>
                <div class="col-lg-12 col-12 form-group">
                    <label>Amount</label>
                    <input type="text" value="'.$get->amount.'" class="form-control" name="amount">
                </div>
            ';
        }
        return response($output);
    }
    public function billing(Request $request){
        $getUsers = User::where('role',2)->get();
        foreach ($getUsers as $getUser){
            $getExistingInvoice = Invoice::where('user_id',$getUser->id)->where('status',0)->latest('id')->first();
            if ($getExistingInvoice){
                $currentBalance = $getUser->balance;
                $packageAmount = $getUser->package_amount;
                $newBalance = $currentBalance + $packageAmount;
                $date1 = $getUser->payment_date;
                $date2 =$getUser->due_date;

                $diff = abs(strtotime($date2) - strtotime($date1));

                $years = floor($diff / (365*60*60*24));
                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                if ($months==1){
                    $usage_time = $days+30;
                }
                else{
                    $usage_time = $days;
                }
                $createInvoice = Invoice::create([
                    'invoice_date'=>$getUser->due_date,
                    'amount'=>$getUser->package_amount,
                    'user_id'=>$getUser->id,
                    'usage_time'=>$getExistingInvoice->usage_time + $usage_time,
                    'balance'=>$getUser->package_amount,
                    'status'=>0,
                    'statas'=>0,
                ]);
                $updateDueDate = User::where('id',$getUser->id)->update(['due_date'=>'Pay First']);
                $updateBalance = User::where('id',$getUser->id)->update(['balance'=>$newBalance]);
                $updateAmount = User::where('id',$getUser->id)->update(['amount'=>0]);
                $updatePaymentDate = User::where('id',$getUser->id)->update(['payment_date'=>0]);
            }
            else{
                $currentBalance = $getUser->balance;
                $packageAmount = $getUser->package_amount;
                $newBalance = $currentBalance + $packageAmount;
                $date1 = $getUser->payment_date;
                $date2 =$getUser->due_date;

                $diff = abs(strtotime($date2) - strtotime($date1));

                $years = floor($diff / (365*60*60*24));
                $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                if ($months==1){
                    $usage_time = $days+30;
                }
                else{
                    $usage_time = $days;
                }
                if ($newBalance<=0){
                    $createInvoice = Invoice::create([
                        'invoice_date'=>$getUser->due_date,
                        'amount'=>$getUser->package_amount,
                        'user_id'=>$getUser->id,
                        'usage_time'=>$usage_time,
                        'balance'=>0,
                        'status'=>1,
                        'statas'=>0,
                    ]);
                    $storeCash = Payment::create([
                        'user_id'=>$getUser->id,
                        'invoice_id'=>$createInvoice->id,
                        'amount'=>$getUser->package_amount,
                        'invoice_balance'=>$newBalance,
                        'date'=>$getUser->due_date,
                        'payment_method'=>'balance Carry Over',
                        'status'=>1,
                    ]);
                    $updateCashId = Invoice::where('id',$createInvoice->id)->update(['payment_id'=>$storeCash->id]);
                    $nextDate =  date('Y-m-d', strtotime($getUser->due_date. ' + 1 month'));
                    $updateBalance = User::where('id',$getUser->id)->update(['balance'=>$newBalance]);
                    $updateAmount = User::where('id',$getUser->id)->update(['amount'=>$storeCash->amount]);
                    $updatePaymentDate = User::where('id',$getUser->id)->update(['payment_date'=>$storeCash->date]);
                    $updateDueDate = User::where('id',$getUser->id)->update(['due_date'=>$nextDate]);
                }
                else{
                    if ($currentBalance<0){
                        $createInvoice = Invoice::create([
                            'invoice_date'=>$getUser->due_date,
                            'amount'=>$getUser->package_amount,
                            'user_id'=>$getUser->id,
                            'usage_time'=>$usage_time,
                            'balance'=>$newBalance,
                            'status'=>0,
                            'statas'=>0,
                        ]);
                        $storeCash = Payment::create([
                            'user_id'=>$getUser->id,
                            'invoice_id'=>$createInvoice->id,
                            'amount'=>$currentBalance * -1,
                            'invoice_balance'=>$newBalance,
                            'date'=>$getUser->due_date,
                            'payment_method'=>'Balance Carry Over',
                            'status'=>1,
                        ]);
                        $nextDate =  date('m/d/Y', strtotime($getUser->due_date. ' + 1 month'));
                        $updateBalance = User::where('id',$getUser->id)->update(['balance'=>$newBalance]);
                        $updateAmount = User::where('id',$getUser->id)->update(['amount'=>0]);
                        $updatePaymentDate = User::where('id',$getUser->id)->update(['payment_date'=>0]);
                        $updateDueDate = User::where('id',$getUser->id)->update(['due_date'=>$nextDate]);
                    }
                    else{
                        $createInvoice = Invoice::create([
                            'invoice_date'=>$getUser->due_date,
                            'amount'=>$getUser->package_amount,
                            'user_id'=>$getUser->id,
                            'usage_time'=>$usage_time,
                            'balance'=>$newBalance,
                            'status'=>0,
                            'statas'=>0,
                        ]);
                        $nextDate =  date('d/m/Y', strtotime($getUser->due_date. ' + 1 month'));
                        $updateBalance = User::where('id',$getUser->id)->update(['balance'=>$newBalance]);
                        $updateAmount = User::where('id',$getUser->id)->update(['amount'=>0]);
                        $updatePaymentDate = User::where('id',$getUser->id)->update(['payment_date'=>0]);
                        $updateDueDate = User::where('id',$getUser->id)->update(['due_date'=>$nextDate]);
                    }

                }


            }

        }

        return redirect()->back()->with('success','ALL CUSTOMERS BILLED SUCCESSFULLY');
    }
    public function billingEach(Request $request){
        $getExistingInvoice = Invoice::where('user_id',$request->id)->where('status',0)->first();
        $getUser = User::find($request->id);
        if ($getExistingInvoice){
            if ($getUser->payment_date==0){
                $nextDatePrototype =  date('m/d/Y', strtotime($getUser->due_date. ' - 1 month'));
                $date1 =$nextDatePrototype;
                $date2 =$getUser->due_date;
            }
            else{
                $date1 = $getUser->payment_date;
                $date2 =$getUser->due_date;
            }

            $diff = abs(strtotime($date2) - strtotime($date1));

            $years = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
            if ($months>=1){
                $usage_time = $days+30;
            }
            else{
                $usage_time = $days;
            }
            $total = $request->amount + $request->package_amount;
            $createInvoice = Invoice::create([
                'invoice_date'=>$request->due_date,
                'amount'=>$request->package_amount,
                'user_id'=>$request->id,
                'usage_time'=>$getExistingInvoice->usage_time + $usage_time,
                'balance'=>$total,
                'status'=>0,
                'statas'=>0,
            ]);
            $terminateInvoice = Invoice::where('id',$getExistingInvoice->id)->update(['status'=>2]);
            $nextDate =  date('m/d/Y', strtotime($getUser->due_date. ' + 1 month'));
            $updateBalance = User::where('id',$getUser->id)->update(['balance'=>$total]);
            $updateAmount = User::where('id',$getUser->id)->update(['amount'=>0]);
            $updatePaymentDate = User::where('id',$getUser->id)->update(['payment_date'=>0]);
            $updateDueDate = User::where('id',$getUser->id)->update(['due_date'=>$nextDate]);
        }
        else{

            $date1 = $getUser->payment_date;
            $date2 =$getUser->due_date;

            $diff = abs(strtotime($date2) - strtotime($date1));

            $years = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
            if ($months==1){
                $usage_time = $days+30;
            }
            else{
                $usage_time = $days;
            }
            $total = $request->amount + $request->package_amount;
            if ($total<=0){
                $createInvoice = Invoice::create([
                    'invoice_date' => $request->due_date,
                    'amount' => $request->package_amount,
                    'user_id' => $request->id,
                    'usage_time' => $usage_time,
                    'balance' => $total,
                    'status' => 1,
                    'statas' => 0,
                ]);
                $createP = Payment::create([
                    'invoice_id'=>$createInvoice->id,
                    'user_id'=>$request->id,
                    'date'=>$request->due_date,
                    'amount'=>$request->package_amount,
                    'status'=>1,
                    'payment_method'=>'Balance Carry Over',
                ]);
                $updateInvoice = Invoice::where('id',$createInvoice->id)->update(['payment_id'=>$createP->id]);
                $nextDate =  date('m/d/Y', strtotime($getUser->due_date. ' + 1 month'));
                $updateBalance = User::where('id',$getUser->id)->update(['balance'=>$total]);
                $updateAmount = User::where('id',$getUser->id)->update(['amount'=>$createP->amount]);
                        $updatePaymentDate = User::where('id',$getUser->id)->update(['payment_date'=>$request->due_date]);
                $updateDueDate = User::where('id',$getUser->id)->update(['due_date'=>$nextDate]);
            }
            else {
                $createInvoice1 = Invoice::create([
                    'invoice_date' => $request->due_date,
                    'amount' => $request->package_amount,
                    'user_id' => $request->id,
                    'usage_time' => $usage_time,
                    'balance' => $total,
                    'status' => 0,
                    'statas' => 0,
                ]);
                $createP1 = Payment::create([
                    'invoice_id'=>$createInvoice1->id,
                    'user_id'=>$request->id,
                    'date'=>$request->due_date,
                    'amount'=>$request->amount*-1,
                    'status'=>1,
                    'payment_method'=>'Balance Carry Over',
                ]);
                $updateInvoice = Invoice::where('id',$createInvoice1->id)->update(['payment_id'=>$createP1->id]);
                $updateInvoice = Invoice::where('id',$createInvoice1->id)->update(['balance'=>$total]);
                $updateInvoice = Invoice::where('id',$createInvoice1->id)->update(['balance'=>$total]);
                $nextDate =  date('m/d/Y', strtotime($request->due_date. ' + 1 month'));
                $updateBalance = User::where('id',$request->id)->update(['balance'=>$total]);
                $updateAmount = User::where('id',$request->id)->update(['amount'=>0]);
                    $updatePaymentDate = User::where('id',$request->id)->update(['payment_date'=>0]);
                $updateDueDate = User::where('id',$request->id)->update(['due_date'=>$nextDate]);
            }
        }

        return redirect()->back()->with('success','CUSTOMER BILLED SUCCESS');
    }
    public function downloadPdf(Request $request){
        // reference the Dompdf namespace

// instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('admin.receipt'));

// (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
        $dompdf->render();

// Output the generated PDF to Browser
        $dompdf->stream();
    }
    public function pdf(){
        return view('admin.pdf');
    }
    public function ttt(){
        return view('admin.ttt');
    }
    public function receipt($id){
        $receipt = Cash::find($id);
        return view('admin.receipt',[
            'receipt'=>$receipt
        ]);
    }
    public function getUserInvoice(Request $request){
        if ($request->ajax()){
            $output = "";
        }
        $user = User::find($request->id);
        $output = '
<input type="hidden" value="'.$user->id.'" name="id">
           <div class="col-lg-12 col-12 form-group">
                                    <label>Location</label>
                                    <input type="text" placeholder="Location" value="'.$user->location.'" class="form-control" name="location">
                                </div>
                                <div class="col-lg-12 col-12 form-group">
                                    <label>Package</label>
                                    <input type="text" placeholder="Package" value="'.$user->bandwidth.'" class="form-control" name="package">
                                </div>
                                   <div class="col-lg-12 col-12 form-group">
                    <label>Pakage Amount</label>
                    <input type="text" value="'.$user->package_amount.'" class="form-control" name="package_amount">
                </div>

                                <div class="col-lg-12 col-12 form-group">
                                    <label>Total Balance</label>
                                    <input type="text" placeholder="Amount" value="'.$user->balance.'" class="form-control" name="amount">
                                </div>
                            </div>
                            <input type="hidden" name="payment_date" value="'.$user->payment_date.'">
                            <input type="hidden" name="due_date" value="'.$user->due_date.'">
        ';
        return response($output);

    }
    public function getInvoice(Request $request){
        if ($request->ajax()){
            $output = "";
        }
        $user = User::find($request->id);
        $output = '
<input type="hidden" value="'.$user->id.'" name="user_id">
           <div class="col-lg-12 col-12 form-group">
                                    <label>Location</label>
                                    <input type="text" placeholder="Location" value="'.$user->location.'" class="form-control" name="location">
                                </div>
                                <div class="col-lg-12 col-12 form-group">
                                    <label>Package</label>
                                    <input type="text" placeholder="Package" value="'.$user->bandwidth.'" class="form-control" name="package">
                                </div>
                                   <div class="col-lg-12 col-12 form-group">
                    <label>Pakage Amount</label>
                    <input type="text" value="'.$user->package_amount.'" class="form-control" name="package_amount">
                </div>

                                <div class="col-lg-12 col-12 form-group">
                                    <label>Total Balance</label>
                                    <input type="text" placeholder="Amount" value="'.$user->balance.'" class="form-control" name="amount">
                                </div>


        ';
        return response($output);

    }
        public function customerDetail($id){
        $user = User::find($id);
        $invoices = Invoice::where('user_id',$user->id)->latest('id')->get();
        return view('admin.customerDetail',[
            'user'=>$user,
            'invoices'=>$invoices
        ]);

    }
    public function expenses(){
        return view('admin.expenses');

    }
    public function addExpense(){
        return view('admin.addExpense');

    }
    public function quotation(){
        $quote = Quotation::where('status',0)->first();
        if ($quote==null){
            $products = Qproduct::where('quotation_id',0)->get();
        }
        else{
            $products = Qproduct::where('quotation_id',$quote->id)->get();
        }
        return view('admin.quotation',[
            'products'=>$products,
            'quote'=>$quote
        ]);

    }
    public function viewQuotation(){
        $quotations = Quotation::where('statas',0)->get();
        $dates = Invoice::all();
        foreach ($dates as $date){

            // Declare and define two dates
            $date1 = strtotime($date->current_time);
            $date2 = strtotime($date->payment_due);

// Formulate the Difference between two dates
            $diff = abs($date2 - $date1);


// To get the year divide the resultant date into
// total seconds in a year (365*60*60*24)
            $years = floor($diff / (365*60*60*24));


// To get the month, subtract it with years and
// divide the resultant date into
// total seconds in a month (30*60*60*24)
            $months = floor(($diff - $years * 365*60*60*24)
                / (30*60*60*24));


// To get the day, subtract it with years and
// months and divide the resultant date into
// total seconds in a days (60*60*24)
            $days = floor(($diff - $years * 365*60*60*24 -
                    $months*30*60*60*24)/ (60*60*24));

            $update = Invoice::where('id',$date->id)->update(['time_difference'=>$days]);
        }
        return view('admin.viewQuotes',[
            'quotations'=>$quotations
        ]);
    }
    public function allQuotes(){
        $quotations = Quotation::all();
        return view('admin.allQuotes',[
            'quotations'=>$quotations
        ]);
    }
    public function expiredQuotes(){
        $quotations = Quotation::where('status');
        return view('admin.expiredQuotes',[
            'quotations'=>$quotations
        ]);
    }
    public function viewInvoice(){
        $quotations = Inv::where('status',0)->get();
        return view('admin.viewInvoice',[
            'quotations'=>$quotations
        ]);
    }
    public function printInvoice($id){
        $quote = Inv::where('id',$id)->first();
        $products = Qproduct::where('quotation_id',$quote->quotation_id)->get();
        $total = Qproduct::where('quotation_id',$quote->quotation_id)->sum('total');
        return view('admin.invoice',[
            'quote'=>$quote,
            'products'=>$products,
            'total'=>$total,
        ]);
    }
    public function allInvoices(){
        $quotations = Invoice::all();
        return view('admin.allInvoice',[
            'quotations'=>$quotations
        ]);
    }
    public function currentDate(Request $request){
        if ($request->ajax()){
            $update = Inv::where('id','>',0)->update(['current_time'=>$request->current]);
            $dates = Inv::all();
            foreach ($dates as $date){

                // Declare and define two dates
                $date1 = strtotime($date->current_time);
                $date2 = strtotime($date->payment_due);

// Formulate the Difference between two dates
                $diff = abs($date2 - $date1);


// To get the year divide the resultant date into
// total seconds in a year (365*60*60*24)
                $years = floor($diff / (365*60*60*24));


// To get the month, subtract it with years and
// divide the resultant date into
// total seconds in a month (30*60*60*24)
                $months = floor(($diff - $years * 365*60*60*24)
                    / (30*60*60*24));


// To get the day, subtract it with years and
// months and divide the resultant date into
// total seconds in a days (60*60*24)
                $days = floor(($diff - $years * 365*60*60*24 -
                        $months*30*60*60*24)/ (60*60*24));
                $update = Inv::where('id',$date->id)->update(['time_difference'=>$days]);
            }
        }
    }
    public function currentDat(Request $request){
        if ($request->ajax()){
            $update = Quotation::where('id','>',0)->update(['current_date'=>$request->current]);
            $dates = Quotation::all();
            foreach ($dates as $date){

                // Declare and define two dates
                $date1 = strtotime($date->current_date);
                $date2 = strtotime($date->expiry_date);

// Formulate the Difference between two dates
                $diff = abs($date1 - $date2);


// To get the year divide the resultant date into
// total seconds in a year (365*60*60*24)
                $years = floor($diff / (365*60*60*24));


// To get the month, subtract it with years and
// divide the resultant date into
// total seconds in a month (30*60*60*24)
                $months = floor(($diff - $years * 365*60*60*24)
                    / (30*60*60*24));


// To get the day, subtract it with years and
// months and divide the resultant date into
// total seconds in a days (60*60*24)
                $days = floor(($diff - $years * 365*60*60*24 -
                        $months*30*60*60*24)/ (60*60*24));
                if ($days>0) {
                    $update = Quotation::where('id', $date->id)->update(['time_difference' => $days]);
                }
            }
        }
    }
    public function quotes($id){
        $quote = Quotation::where('id',$id)->first();
        $products = Qproduct::where('quotation_id',$quote->id)->get();
        $total = Qproduct::where('quotation_id',$quote->id)->sum('total');
        $updateStatus = Quotation::where('status',0)->update(['status'=>1]);
        return view('admin.quotes',[
            'quote'=>$quote,
            'products'=>$products,
            'total'=>$total,
        ]);
    }
    public function invoice(Request $request , $id){
        $storeInvoice = Inv::create([
           'quotation_id'=>$id,
           'invoice_date'=>$request->invoice_date,
           'payment_due'=>$request->payment_due,
           'amount'=>$request->amount,
           'status'=>0,
           'statas'=>0,
        ]);
        $invoiceId = Inv::where('quotation_id',$id)->first();

        $quote = Inv::where('id',$invoiceId->id)->first();
        $products = Qproduct::where('quotation_id',$id)->get();
        $total = Qproduct::where('quotation_id',$id)->sum('total');
        $updateQuotationStatus = Quotation::where('id',$id)->where('status',0)->update(['status'=>1]);
        return view('admin.invoice',[
            'quote'=>$quote,
            'products'=>$products,
            'total'=>$total,
        ]);
    }
    public function storeCustomer(Request $request){
        if ($request->ajax()){
            $output = "";
        }
        $store = User::create([
           'first_name'=>$request->first_name,
           'last_name'=>$request->last_name,
           'email'=>$request->email,
           'phone'=>$request->phone,
           'location'=>$request->location,
           'bandwidth'=>$request->bandwidth,
           'payment_date'=>$request->payment_date,
           'time_difference'=>$request->time_difference,
           'due_date'=>$request->due_date,
           'date_to_send_sms'=>$request->sms_date,
           'amount'=>$request->amount,
           'package_amount'=>$request->amount_supposed_to_pay,
           'amount_supposed_to_be_paid'=>$request->amount_supposed_to_pay - $request->amount,
           'balance'=>$request->amount_supposed_to_pay - $request->amount,
           'role'=>2,
           'password'=>Hash::make('123456'),
        ]);
        $date1 = $request->payment_date;
        $date2 =$request->due_date;

        $diff = abs(strtotime($date2) - strtotime($date1));

        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        if ($months==1){
            $usage_time = $days+30;
        }
        else{
            $usage_time = $days;
        }
        $createInvoice = Invoice::create([
            'invoice_date'=>$request->payment_date,
            'amount'=>$request->amount_supposed_to_pay,
            'user_id'=>$store->id,
            'usage_time'=>$usage_time,
            'balance'=>$request->amount_supposed_to_pay,
            'status'=>0,
            'statas'=>0,
        ]);
        $nextDate =  date('m/d/Y', strtotime($request->due_date));
        $updateBalance = User::where('id',$store->id)->update(['balance'=>$request->amount_supposed_to_pay]);
        $updateAmount = User::where('id',$store->id)->update(['amount'=>0]);
        $updatePaymentDate = User::where('id',$store->id)->update(['payment_date'=>0]);
            $updateDueDate = User::where('id',$store->id)->update(['due_date'=>$nextDate]);

        $getMinUsage = Invoice::where('user_id',$store->id)->where('status',0)->min('usage_time');
        $getInvoice = Invoice::where('user_id',$store->id)->where('status',0)->where('usage_time',$getMinUsage)->first();
        $currentBalance = $getInvoice->balance - $request->amount;
        if ($request->amount==0){

        }
        else{
            if ($currentBalance<=0){
                $createPayment = Cash::create([
                    'user_id'=>$store->id,
                    'invoice_id'=>$getInvoice->id,
                    'amount'=>$request->amount,
                    'date'=>$request->payment_date,
                    'reason'=>'Internet Subscription',
                ]);
                $createPay = Payment::create([
                    'user_id'=>$store->id,
                    'invoice_id'=>$getInvoice->id,
                    'amount'=>$request->amount,
                    'date'=>$request->payment_date,
                    'payment_method'=>'Cash',
                ]);
                $updateBal = Invoice::where('user_id',$store->id)->update(['usage_time'=>10000]);
                $updateStatus = Invoice::where('user_id',$store->id)->update(['status'=>1]);
                $updateBalance = Invoice::where('user_id',$store->id)->update(['balance'=>$currentBalance]);
                $updatePaymentId = Invoice::where('user_id',$store->id)->update(['payment_id'=>$createPay->id]);
                $updateIBalance = Payment::where('invoice_id',$getInvoice->id)->where('id',$createPay->id)->update(['invoice_balance'=>$currentBalance]);
                $updateCashAmount = Invoice::where('user_id',$store->id)->update(['cash_id'=>$createPayment->id]);
                $updateCash = Invoice::where('user_id',$store->id)->update(['cash_amount'=>$request->amount]);
                $updateUserAmount = User::where('id',$store->id)->update(['amount'=>$request->amount]);
                $updateUserDate = User::where('id',$store->id)->update(['payment_date'=>$request->payment_date]);
                $updateUserBalance = User::where('id',$store->id)->update(['balance'=>$currentBalance]);
            }
            else{
                $getInv = Invoice::where('user_id',$store->id)->where('status',0)->first();
                $currentBal = $getInv->balance - $request->amount;
                if ($currentBal>0){
                    $createPayment1 = Cash::create([
                        'user_id'=>$store->id,
                        'invoice_id'=>$getInvoice->id,
                        'amount'=>$request->amount,
                        'date'=>$request->payment_date,
                        'reason'=>'Internet Subscription',
                    ]);
                    $createPay1 = Payment::create([
                        'user_id'=>$store->id,
                        'invoice_id'=>$getInv->id,
                        'amount'=>$request->amount,
                        'date'=>$request->payment_date,
                        'payment_method'=>'Cash',
                    ]);
                    $updateBalance = Invoice::where('id',$getInv->id)->update(['balance'=>$currentBal]);
                    $updateIBalance = Payment::where('invoice_id',$getInv->id)->where('id',$createPay1->id)->update(['invoice_balance'=>$currentBal]);
                    $updateCashAmount = Invoice::where('id',$getInv->id)->update(['payment_id'=>$createPay1->id]);
                    $updateCash = Invoice::where('id',$getInv->id)->update(['cash_amount'=>$request->amount]);
                    $updateUserA = User::where('id',$store->id)->update(['amount'=>$request->amount]);
                    $updateUserD = User::where('id',$store->id)->update(['payment_date'=>$request->payment_date]);
                    $updateUserBal = User::where('id',$store->id)->update(['balance'=>$currentBal]);
                }
            }

        }
    }
    public function dueDate(Request $request){
        if ($request->ajax()){
            $output = "";
        }
        $updateDueDate = User::where('id',$request->id)->first();
        $output = '
        <input type="date" class="form-control"  value="'.$updateDueDate->due_date.'" id="update_due_date"/>
        <input type="hidden" class="form-control"  value="'.$updateDueDate->id.'" id="customer_id"/>

        ';
        return response($output);
    }
    public function getInvoiceId(Request $request){
        if ($request->ajax()){
            $output = "";
            $getId = Invoice::find($request->id);
            $output = '
                <label><b>'.$getId->quotation->name.'</b></label>
              <input type="date" class="form-control" name="date"/>
                            <input type="hidden" value="'.$getId->id.'" name="invoice_id">
            ';
        }
        return response($output);
    }
    public function updateInvoiceDueDate(Request $request){
        $getId = Invoice::find($request->invoice_id);
        $updatePaymenDue = Invoice::where('id',$request->invoice_id)->update(['payment_due'=>$request->date]);
        return redirect(url('viewInvoice'))->with('success','Payment Due Date Updated Success');
    }
    public function updateDueDate(Request $request){
        if ($request->ajax()){
            $output = "";
        }
        $updateDueDate =User::where('id',$request->id)->update(['due_date'=>$request->due_date]);
        $updateTimeDifference =User::where('id',$request->id)->update(['time_difference'=>$request->time_difference]);
        $output=$request->time_difference;
        return response($output);
    }
            public function makeCashPayment(Request $request){
        $getMinUsage = Invoice::where('user_id',$request->user_id)->where('status',0)->min('usage_time');
        $getInvoice = Invoice::where('user_id',$request->user_id)->where('status',0)->where('usage_time',$getMinUsage)->first();
        if ($getInvoice){
            $currentBalance = $getInvoice->balance - $request->amount;
            $createPayment = Cash::create([
                'user_id'=>$request->user_id,
                'invoice_id'=>$getInvoice->id,
                'amount'=>$request->amount,
                'date'=>$request->payment_date,
                'reason'=>'Internet Subscription',
            ]);
            $createPay = Payment::create([
                'user_id'=>$request->user_id,
                'invoice_id'=>$getInvoice->id,
                'reference'=>'cash_payment',
                'date'=>$request->payment_date,
                'amount'=>$request->amount,
                'status'=>1,
                'payment_method'=>'Cash',

            ]);
            $updateBalance = Invoice::where('user_id',$request->user_id)->where('status',0)->where('usage_time',$getMinUsage)->update(['balance'=>$currentBalance]);
            $updateIBalance = Payment::where('invoice_id',$getInvoice->id)->where('id',$createPay->id)->update(['invoice_balance'=>$currentBalance]);
            $updateCashAmount = Invoice::where('user_id',$request->user_id)->where('status',0)->where('usage_time',$getMinUsage)->update(['cash_id'=>$createPayment->id]);
            $updatePaymentId = Invoice::where('user_id',$request->user_id)->where('status',0)->where('usage_time',$getMinUsage)->update(['payment_id'=>$createPay->id]);
            $updateCash = Invoice::where('user_id',$request->user_id)->where('status',0)->where('usage_time',$getMinUsage)->update(['cash_amount'=>$request->amount]);
            $updateUserAmount = User::where('id',$request->user_id)->update(['amount'=>$request->amount]);
            $updateUserDate = User::where('id',$request->user_id)->update(['payment_date'=>$request->payment_date]);
            $userBalance = Invoice::where('user_id',$request->user_id)->where('status',0)->sum('balance');
            $updateUserBalance = User::where('id',$request->user_id)->update(['balance'=>$userBalance]);
            $getInv = Invoice::where('user_id',$request->user_id)->where('status',0)->where('usage_time',$getMinUsage)->first();
            if ($getInv->balance==0){
                $updateBal = Invoice::where('id',$getInv->id)->update(['usage_time'=>2147483647]);
                $updateStatus = Invoice::where('id',$getInv->id)->update(['status'=>1]);
            }
            else{
                if ($getInv->balance<0){
                    $updateBal = Invoice::where('id',$getInv->id)->update(['usage_time'=>2147483647]);
                    $updateStatus = Invoice::where('id',$getInv->id)->update(['status'=>1]);
                    $getMinUs = Invoice::where('user_id',$request->user_id)->where('status',0)->min('usage_time');
                    $getIn = Invoice::where('user_id',$request->user_id)->where('status',0)->where('usage_time',$getMinUs)->first();
                    $getI = Invoice::where('user_id',$request->user_id)->where('balance','<',0)->first();
                    if ($getIn){
                        $currentBal = $getIn->balance + $getI->balance;
                        $createPay1 = Payment::create([
                            'user_id'=>$request->user_id,
                            'invoice_id'=>$getIn->id,
                            'reference'=>'cash_payment',
                            'date'=>$request->payment_date,
                            'amount'=>$getI->balance * -1,
                            'status'=>1,
                            'payment_method'=>'Cash',

                        ]);
                        $updateB = Invoice::where('id',$getIn->id)->where('status',0)->where('usage_time',$getMinUs)->update(['balance'=>$currentBal]);
                        $updateIB= Payment::where('invoice_id',$getIn->id)->where('id',$createPay1->id)->update(['invoice_balance'=>$currentBal]);
                        $updateCashA = Invoice::where('id',$getIn->id)->where('status',0)->where('usage_time',$getMinUs)->update(['payment_id'=>$createPay1->id]);
                        $updateC = Invoice::where('id',$getIn->id)->where('status',0)->where('usage_time',$getMinUs)->update(['cash_amount'=>-($getI->balance)]);
                        $updateUserA = User::where('id',$getIn->user_id)->update(['amount'=>$createPay1->amount]);
                        $updateUserD = User::where('id',$getIn->user_id)->update(['payment_date'=>$request->payment_date]);
                        $userBal= Invoice::where('user_id',$getIn->user_id)->where('status',0)->sum('balance');
                        $updateUserBal = User::where('id',$getIn->user_id)->update(['balance'=>$userBal]);
                        $updateB = Invoice::where('id',$getI->id)->update(['balance'=>0]);
                        $getMinUs1 = Invoice::where('user_id',$request->user_id)->where('status',0)->min('usage_time');
                        $getIn1 = Invoice::where('user_id',$request->user_id)->where('status',0)->where('usage_time',$getMinUs1)->first();
                        if ($getIn1->balance==0){
                            $updateBal = Invoice::where('id',$getIn1->id)->update(['usage_time'=>2147483647]);
                            $updateStatus = Invoice::where('id',$getIn1->id)->update(['status'=>1]);
                        }
                        else{
                            if ($getIn1->balance<0){
                                $updateBal = Invoice::where('id',$getIn1->id)->update(['usage_time'=>2147483647]);
                                $updateStatus = Invoice::where('id',$getIn1->id)->update(['status'=>1]);
                                $getMinUs2 = Invoice::where('user_id',$request->user_id)->where('status',0)->min('usage_time');
                                $getIn2 = Invoice::where('user_id',$request->user_id)->where('status',0)->where('usage_time',$getMinUs2)->first();
                                $getI2 = Invoice::where('user_id',$request->user_id)->where('balance','<',0)->first();
                                if ($getIn2){
                                    $currentBal1 = $getIn2->balance + $getI2->balance;
                                    $createPay2 = Payment::create([
                                        'user_id'=>$request->user_id,
                                        'invoice_id'=>$getIn2->id,
                                        'reference'=>'cash_payment',
                                        'date'=>$request->payment_date,
                                        'amount'=>$getI2->balance * -1,
                                        'status'=>1,
                                        'payment_method'=>'Cash',

                                    ]);
                                    $updateB2 = Invoice::where('id',$getIn2->id)->where('status',0)->where('usage_time',$getMinUs2)->update(['balance'=>$currentBal1]);
                                    $updateIB2= Payment::where('invoice_id',$getIn2->id)->where('id',$createPay2->id)->update(['invoice_balance'=>$currentBal1]);
                                    $updateCashA2 = Invoice::where('id',$getIn2->id)->where('status',0)->where('usage_time',$getMinUs2)->update(['payment_id'=>$createPay2->id]);
                                    $updateC2 = Invoice::where('user_id',$getIn2->id)->where('status',0)->where('usage_time',$getMinUs2)->update(['cash_amount'=>-($getI2->balance)]);
                                    $updateUserA2 = User::where('id',$getIn2->user_id)->update(['amount'=>$createPay2->amount]);
                                    $updateUserD2 = User::where('id',$getIn2->user_id)->update(['payment_date'=>$request->payment_date]);
                                    $userBal1= Invoice::where('user_id',$getIn2->user_id)->where('status',0)->sum('balance');
                                    $updateUserBal1 = User::where('id',$getIn2->user_id)->update(['balance'=>$userBal1]);
                                    $updateB2 = Invoice::where('id',$getI2->id)->update(['balance'=>0]);
                                    $getMinUs2 = Invoice::where('user_id',$request->user_id)->where('status',0)->min('usage_time');
                                    $getIn2 = Invoice::where('user_id',$request->user_id)->where('status',0)->where('usage_time',$getMinUs2)->first();
                                    if ($getIn2->balance==0){
                                        $updateBal = Invoice::where('id',$getIn2->id)->update(['usage_time'=>2147483647]);
                                        $updateStatus = Invoice::where('id',$getIn2->id)->update(['status'=>1]);
                                    }
                                    else{
                                        if ($getIn2->balance<0){
                                            $updateBal = Invoice::where('id',$getIn2->id)->update(['usage_time'=>2147483647]);
                                            $updateStatus = Invoice::where('id',$getIn2->id)->update(['status'=>1]);
                                            $getMinUs3 = Invoice::where('user_id',$request->user_id)->where('status',0)->min('usage_time');
                                            $getIn3 = Invoice::where('user_id',$request->user_id)->where('status',0)->where('usage_time',$getMinUs3)->first();
                                            $getI3 = Invoice::where('user_id',$request->user_id)->where('balance','<',0)->first();
                                            if ($getIn3){
                                                $currentBal2 = $getIn3->balance + $getI3->balance;
                                                $createPay3 = Payment::create([
                                                    'user_id'=>$request->user_id,
                                                    'invoice_id'=>$getIn3->id,
                                                    'reference'=>'cash_payment',
                                                    'date'=>$request->payment_date,
                                                    'amount'=>$getI3->balance * -1,
                                                    'status'=>1,
                                                    'payment_method'=>'Cash',

                                                ]);
                                                $updateB2 = Invoice::where('id',$getIn3->id)->where('status',0)->where('usage_time',$getMinUs3)->update(['balance'=>$currentBal2]);
                                                $updateIB2= Payment::where('invoice_id',$getIn3->id)->where('id',$createPay3->id)->update(['invoice_balance'=>$currentBal2]);
                                                $updateCashA2 = Invoice::where('id',$getIn3->id)->where('status',0)->where('usage_time',$getMinUs3)->update(['payment_id'=>$createPay3->id]);
                                                $updateC2 = Invoice::where('user_id',$getIn3->id)->where('status',0)->where('usage_time',$getMinUs3)->update(['cash_amount'=>-($getI3->balance)]);
                                                $updateUserA2 = User::where('id',$getIn3->user_id)->update(['amount'=>$createPay3->amount]);
                                                $updateUserD2 = User::where('id',$getIn3->user_id)->update(['payment_date'=>$request->payment_date]);
                                                $userBal1= Invoice::where('user_id',$getIn3->user_id)->where('status',0)->sum('balance');
                                                $updateUserBal1 = User::where('id',$getIn3->user_id)->update(['balance'=>$userBal1]);
                                                $updateB2 = Invoice::where('id',$getI3->id)->update(['balance'=>0]);
                                            }
                                            else{
                                                $updateUserBal1 = User::where('id',$request->user_id)->update(['balance'=>$getI3->balance]);

                                            }
                                        }

                                    }
                                }
                                else{
                                    $updateUserBal1 = User::where('id',$request->user_id)->update(['balance'=>$getI2->balance]);

                                }

                            }

                        }
                    }
                    else{
                        $updateUserBal1 = User::where('id',$request->user_id)->update(['balance'=>$getI->balance]);

                    }

                }
            }
        }
        else{
            $getUser = User::find($request->user_id);
            $getCurrectInvoice = Invoice::where('user_id',$request->user_id)->where('status',1)->latest('id')->first();
            $currenUserBalance = $getUser->balance - $request->amount;
            $createCash = Cash::create([
                'user_id'=>$request->user_id,
                'invoice_id'=>$getCurrectInvoice->id,
                'amount'=>$request->amount,
                'date'=>$request->payment_date,
                'reason'=>'Internet Subscription',
            ]);
            $createPay = Payment::create([
                'user_id'=>$request->user_id,
                'invoice_id'=>$getCurrectInvoice->id,
                'reference'=>'cash_payment',
                'date'=>$request->payment_date,
                'amount'=>$request->amount,
                'status'=>1,
                'payment_method'=>'Cash',

            ]);
            $updateIBalance = Payment::where('invoice_id',$getCurrectInvoice->id)->where('id',$createPay->id)->update(['invoice_balance'=>$currenUserBalance]);
            $updateCashAmount = Invoice::where('user_id',$request->user_id)->where('status',1)->update(['cash_id'=>$createCash->id]);
            $updatePaymentId = Invoice::where('user_id',$request->user_id)->where('status',1)->update(['payment_id'=>$createPay->id]);
            $updateCash = Invoice::where('user_id',$request->user_id)->where('status',1)->update(['cash_amount'=>$request->amount]);
            $updateUserAmount = User::where('id',$request->user_id)->update(['amount'=>$request->amount]);
            $updateUserDate = User::where('id',$request->user_id)->update(['payment_date'=>$request->payment_date]);
            $userBalance = Invoice::where('user_id',$request->user_id)->where('status',1)->sum('balance');
            $updateUserBalance = User::where('id',$request->user_id)->update(['balance'=>$currenUserBalance]);
        }




        return redirect()->back()->with('success','PAYMENT ADDED SUCCESS');
    }
    public function invoicePayment($id){
        $cashs = Payment::where('invoice_id',$id)->get();
        $invoice = Invoice::find($id);
        return view('admin.invoicePayment',[
            'cashs'=>$cashs,
            'invoice'=>$invoice
        ]);
    }
    public function filterInvoice(Request $request){
        $user = User::find($request->user_id);
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $start_date = date("m/d/Y", strtotime($startDate));
        $end_date = date("m/d/Y", strtotime($endDate));
        $invoices  = Invoice::whereBetween('invoice_date', array($start_date, $end_date))->where('user_id',$user->id)->get();
        return view('admin.customerDetail',[
            'invoices'=>$invoices,
            'user'=>$user,
        ]);
    }
    public function filterMpesa(Request $request){
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $start_date = date("d/m/Y", strtotime($startDate));
        $end_date = date("d/m/Y", strtotime($endDate));
        $invoices  = Mpesa::whereBetween('originationTime', array($start_date, $end_date))->get();
        $total  = Mpesa::whereBetween('originationTime', array($start_date, $end_date))->sum('amount');
        return view('admin.mpesa',[
            'mpesas'=>$invoices,
            'total'=>$total,
        ]);
    }
    public function getReceipt($id){
        $receipt = Payment::find($id);
        return view('admin.rec',[
            'receipt'=>$receipt
        ]);
    }
    public function cashReceipt($id){
        $receipt = Cash::find($id);
        return view('admin.cashReceipt',[
            'receipt'=>$receipt
        ]);
    }
    public function mpesaReceipt($id){
        $receipt = Mpesa::find($id);
        return view('admin.mpesaReceipt',[
            'receipt'=>$receipt
        ]);
    }
}
