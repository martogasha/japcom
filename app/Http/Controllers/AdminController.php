<?php

namespace App\Http\Controllers;

use App\Cat;
use App\Models\Cash;
use App\Models\Invoice;
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
    public function billing(Request $request){

        $getUsers = User::all();
        foreach ($getUsers as $getUser){
            $currentBalance = $getUser->balance;
            $packageAmount = $getUser->package_amount;
            $newBalance = $currentBalance + $packageAmount;
            $updateBalance = User::where('id',$getUser->id)->update(['balance'=>$newBalance]);
        }

        return redirect()->back()->with('success','ALL CUSTOMERS BILLED SUCCESSFULLY');
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
           <div class="col-lg-12 col-12 form-group">
                                    <label>Location</label>
                                    <input type="text" placeholder="Location" value="'.$user->location.'" class="form-control" name="location">
                                </div>
                                <div class="col-lg-12 col-12 form-group">
                                    <label>Package</label>
                                    <input type="text" placeholder="Package" value="'.$user->bandwidth.'" class="form-control" name="package">
                                </div>
                                <div class="col-lg-12 col-12 form-group">
                                    <label>Amount</label>
                                    <input type="text" placeholder="Amount" value="'.$user->balance.'" class="form-control" name="amount">
                                </div>
        ';
        return response($output);

    }
    public function customerDetail($id){
        $user = User::find($id);
        $cashes = Cash::where('user_id',$user->id)->get();
        return view('admin.customerDetail',[
            'user'=>$user,
            'cashes'=>$cashes
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
        $quotations = Invoice::where('status',0)->get();
        return view('admin.viewInvoice',[
            'quotations'=>$quotations
        ]);
    }
    public function printInvoice($id){
        $quote = Invoice::where('id',$id)->first();
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
            $update = Invoice::where('id','>',0)->update(['current_time'=>$request->current]);
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
        $storeInvoice = Invoice::create([
           'quotation_id'=>$id,
           'invoice_date'=>$request->invoice_date,
           'payment_due'=>$request->payment_due,
           'amount'=>$request->amount,
           'status'=>0,
           'statas'=>0,
        ]);
        $invoiceId = Invoice::where('quotation_id',$id)->first();

        $quote = Invoice::where('id',$invoiceId->id)->first();
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
        $user = User::find($request->user_id);
        $supposed_to_pay = $user->amount_supposed_to_be_paid;
        $final_supposed_to_pay = $supposed_to_pay - $request->amount;
        $update_balance = $user->balance - $request->amount;
        $amount = $request->amount;
        if ($final_supposed_to_pay>=0){
            $updateSupposedToPay = User::where('id', $user->id)->update(['amount_supposed_to_be_paid' => $final_supposed_to_pay]);
        }
        else{
            $updateSupposedToPay = User::where('id', $user->id)->update(['amount_supposed_to_be_paid' => 0]);

        }
        $updateBalance = User::where('id',$user->id)->update(['balance'=>$update_balance]);
        $updateAmount = User::where('id',$user->id)->update(['amount'=>$amount]);
        $storeCash = Cash::create([
           'user_id'=>$request->user_id,
           'amount'=>$request->amount,
           'date'=> date("m/d/Y"),
        ]);
        return redirect()->back()->with('success','PAYMENT ADDED SUCCESS');
    }
}
