<?php

namespace App\Http\Controllers;

use App\Exceptions\Controller;
use App\Models\Invoice;
use App\Models\Mpesa;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    public function index(){
        if (\Illuminate\Support\Facades\Auth::check()){
            $mpesas = Mpesa::where('id','>',0)->orderByDesc('id')->get();
            $currentMonth = date('m');
            $total = Mpesa::where('currentMonth',$currentMonth)->sum('amount');
            return view('admin.mpesa',[
                'mpesas'=>$mpesas,
                'total'=>$total,
            ]);
        }
        else{
            return redirect(url('login'));
        }

    }
    public function subscribe(){
//YOU MPESA API KEYS
        $consumerKey = "HZKs4kTilx4xoc8CGKgR8t3Jkxe6A5Yp"; //Fill with your app Consumer Key
        $consumerSecret = "R2xDmkzkVtBAeU4C"; //Fill with your app Consumer Secret
//ACCESS TOKEN URL
        $access_token_url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $headers = ['Content-Type:application/json; charset=utf8'];
        $curl = curl_init($access_token_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result = json_decode($result);
        $access_token = $result->access_token;
        Log::info($access_token);
        $registerurl = 'https://api.safaricom.co.ke/mpesa/c2b/v2/registerurl';
        $BusinessShortCode = '6589582';
        $confirmationUrl = 'https://admin.dolextech.com/api/storeWebhooks';
        $validationUrl = 'https://admin.dolextech.com/api/authenticate';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $registerurl);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json; charset=utf8',
            'Authorization:Bearer ' . $access_token
        ));
        $data = array(
            'ShortCode' => $BusinessShortCode,
            'ResponseType' => 'Completed',
            'ConfirmationURL' => $confirmationUrl,
            'ValidationURL' => $validationUrl
        );
        $data_string = json_encode($data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        echo $curl_response = curl_exec($curl);


    }
    public function storeWebhooks(Request $request)
    {
        $input = $request->all();
        $dateFormat = $input['TransTime'];
        $chechIfExists = Mpesa::where('reference', $input['TransID'])->first();
        $currentMonth = date('m');
        $currentYear = date('Y');
        if (is_null($chechIfExists)) {
            $getUserIdentification = User::where('phone', $input['BillRefNumber'])->orWhere('phoneOne', $input['BillRefNumber'])->first();
            if (!is_null($getUserIdentification)){
                $getInvoice = Invoice::where('user_id', $getUserIdentification->id)->where('status', 0)->first();
                if (!is_null($getInvoice)) {
                    $chechIfExist = Mpesa::where('reference', $input['TransID'])->first();
                    if (is_null($chechIfExist)){
                        $currentBalance = $getInvoice->balance - $input['TransAmount'];
                        $createPayment = Mpesa::create([
                            'reference' => $input['TransID'],
                            'originationTime' => date("d-m-Y", strtotime($dateFormat)),
                            'senderFirstName' => $input['FirstName'],
                            'senderMiddleName' => '',
                            'senderLastName' => '',
                            'senderPhoneNumber' => $input['MSISDN'],
                            'amount' => $input['TransAmount'],
                            'status' => '',
                            'system' => '',
                            'currency' => '',
                            'invoice_id' => $getInvoice->id,
                            'currentMonth' =>$currentMonth,
                            'currentYear' =>$currentYear,

                        ]);
                        $createPay = Payment::create([
                            'user_id' => $getUserIdentification->id,
                            'invoice_id' => $getInvoice->id,
                            'reference' => $input['TransID'],
                            'date' => $createPayment->originationTime,
                            'amount' => $createPayment->amount,
                            'status' => 1,
                            'payment_method' => 'Mpesa',
                            'currentMonth' =>$currentMonth,
                        ]);
                        $updateInvoiceBalance = Invoice::where('id', $getInvoice->id)->update(['balance' => $currentBalance]);
                        $updateInvoicePaymentId = Invoice::where('id', $getInvoice->id)->update(['payment_id' => $createPay->id]);
                        $updateInvoiceMId = Invoice::where('id', $getInvoice->id)->update(['mpesa_id' => $createPayment->id]);
                        $updateInvoiceMAmount = Invoice::where('id', $getInvoice->id)->update(['mpesa_amount' => $createPayment->amount]);
                        $updateIBalance = Payment::where('id', $createPay->id)->update(['invoice_balance' => $currentBalance]);
                        $updateUserAmount = User::where('id', $getUserIdentification->id)->update(['amount' => $createPayment->amount]);
                        $updateUserDate = User::where('id', $getUserIdentification->id)->update(['payment_date' => $createPay->date]);
                        $updateUserBalance = User::where('id', $getUserIdentification->id)->update(['balance' => $currentBalance]);
                        $getInv = Invoice::where('user_id', $getUserIdentification->id)->where('status', 0)->first();
                        if ($getInv->balance == 0) {
                            $updateBal = Invoice::where('id', $getInv->id)->update(['usage_time' => 2147483647]);
                            $updateStatus = Invoice::where('id', $getInv->id)->update(['status' => 1]);
                        } else {
                            if ($getInv->balance < 0) {
                                $updateBal = Invoice::where('id', $getInv->id)->update(['usage_time' => 2147483647]);
                                $updateStatus = Invoice::where('id', $getInv->id)->update(['status' => 1]);
                                $getIn = Invoice::where('user_id', $getUserIdentification->id)->where('status', 0)->first();
                                $getI = Invoice::where('user_id', $getUserIdentification->id)->where('balance', '<', 0)->first();
                                if ($getIn) {
                                    $currentBal = $getIn->balance + $getI->balance;
                                    $createPay1 = Payment::create([
                                        'user_id' => $getUserIdentification->id,
                                        'invoice_id' => $getIn->id,
                                        'reference' => $input['TransID'],
                                        'date' => date("d-m-Y", strtotime($dateFormat)),
                                        'amount' => $getI->balance * -1,
                                        'status' => 1,
                                        'payment_method' => 'Mpesa',

                                    ]);
                                    $updateB = Invoice::where('id', $getIn->id)->where('status', 0)->update(['balance' => $currentBal]);
                                    $updateIB = Payment::where('invoice_id', $getIn->id)->where('id', $createPay1->id)->update(['invoice_balance' => $currentBal]);
                                    $updateInvoicePayment = Invoice::where('id', $getIn->id)->where('status', 0)->update(['payment_id' => $createPay1->id]);
                                    $updateC = Invoice::where('id', $getIn->id)->where('status', 0)->update(['mpesa_amount' => -($getI->balance)]);
                                    $updateUserA = User::where('id', $getIn->user_id)->update(['amount' => $createPay1->amount]);
                                    $updateUserD = User::where('id', $getIn->user_id)->update(['payment_date' => $createPay1->date]);
                                    $userBal = Invoice::where('user_id', $getIn->user_id)->where('status', 0)->sum('balance');
                                    $updateUserBal = User::where('id', $getIn->user_id)->update(['balance' => $userBal]);
                                    $updateB = Invoice::where('id', $getI->id)->update(['balance' => 0]);
                                    $getMinUs1 = Invoice::where('user_id', $getUserIdentification->id)->where('status', 0)->min('usage_time');
                                    $getIn1 = Invoice::where('user_id', $getUserIdentification->id)->where('status', 0)->where('usage_time', $getMinUs1)->first();
                                    if ($getIn1->balance == 0) {
                                        $updateCashA = Invoice::where('id', $getIn->id)->where('status', 0)->update(['mpesa_id' => $createPay->id]);
                                        $updateBal = Invoice::where('id', $getIn1->id)->update(['usage_time' => 2147483647]);
                                        $updateStatus = Invoice::where('id', $getIn1->id)->update(['status' => 1]);
                                    } else {
                                        if ($getIn1->balance < 0) {
                                            $updateBal = Invoice::where('id', $getIn1->id)->update(['usage_time' => 2147483647]);
                                            $updateStatus = Invoice::where('id', $getIn1->id)->update(['status' => 1]);
                                            $getMinUs2 = Invoice::where('user_id', $getUserIdentification->id)->where('status', 0)->min('usage_time');
                                            $getIn2 = Invoice::where('user_id', $getUserIdentification->id)->where('status', 0)->where('usage_time', $getMinUs2)->first();
                                            $getI2 = Invoice::where('user_id', $getUserIdentification->id)->where('balance', '<', 0)->first();
                                            if ($getIn2) {
                                                $currentBal1 = $getIn2->balance + $getI2->balance;
                                                $createP = Payment::create([
                                                    'user_id' => $getUserIdentification->id,
                                                    'invoice_id' => $getIn2->id,
                                                    'reference' => $input['TransID'],
                                                    'date' => date("d-m-Y", strtotime($dateFormat)),
                                                    'amount' => $getI2->balance * -1,
                                                    'status' => 1,
                                                    'payment_method' => 'Mpesa',
                                                ]);
                                                $updateB2 = Invoice::where('id', $getIn2->id)->where('status', 0)->where('usage_time', $getMinUs2)->update(['balance' => $currentBal1]);
                                                $updateIB2 = Payment::where('invoice_id', $getIn2->id)->where('id', $createP->id)->update(['invoice_balance' => $currentBal1]);
                                                $updateC2 = Invoice::where('user_id', $getIn2->id)->where('status', 0)->where('usage_time', $getMinUs2)->update(['mpesa_amount' => -($getI2->balance)]);
                                                $updatePaymentId = Invoice::where('user_id', $getIn2->id)->where('status', 0)->where('usage_time', $getMinUs2)->update(['payment_id' => $createP->id]);
                                                $updateUserA2 = User::where('id', $getIn2->user_id)->update(['amount' => $createP->amount]);
                                                $updateUserD2 = User::where('id', $getIn2->user_id)->update(['payment_date' => $createP->date]);
                                                $userBal1 = Invoice::where('user_id', $getIn2->user_id)->where('status', 0)->sum('balance');
                                                $updateUserBal1 = User::where('id', $getIn2->user_id)->update(['balance' => $userBal1]);
                                                $updateB2 = Invoice::where('id', $getI2->id)->update(['balance' => 0]);
                                                $getMinUs2 = Invoice::where('user_id', $getUserIdentification->id)->where('status', 0)->min('usage_time');
                                                $getIn2 = Invoice::where('user_id', $getUserIdentification->id)->where('status', 0)->where('usage_time', $getMinUs2)->first();
                                                if ($getIn2->balance == 0) {
                                                    $updateBal = Invoice::where('id', $getIn2->id)->update(['usage_time' => 2147483647]);
                                                    $updateStatus = Invoice::where('id', $getIn2->id)->update(['status' => 1]);
                                                } else {
                                                    if ($getIn2->balance < 0) {
                                                        $updateBal = Invoice::where('id', $getIn2->id)->update(['usage_time' => 2147483647]);
                                                        $updateStatus = Invoice::where('id', $getIn2->id)->update(['status' => 1]);
                                                        $getMinUs3 = Invoice::where('user_id', $getUserIdentification->id)->where('status', 0)->min('usage_time');
                                                        $getIn3 = Invoice::where('user_id', $getUserIdentification->id)->where('status', 0)->where('usage_time', $getMinUs3)->first();
                                                        $getI3 = Invoice::where('user_id', $getUserIdentification->id)->where('balance', '<', 0)->first();
                                                        if ($getIn3) {
                                                            $currentBal2 = $getIn3->balance + $getI3->balance;
                                                            $createP1 = Payment::create([
                                                                'invoice_id' => $getIn3->id,
                                                                'user_id' => $getUserIdentification->id,
                                                                'reference' => $input['TransID'],
                                                                'date' => date("d-m-Y", strtotime($dateFormat)),
                                                                'amount' => $getI3->balance * -1,
                                                                'status' => 1,
                                                                'payment_method' => 'Mpesa',
                                                                'currentMonth' =>$currentMonth,
                                                            ]);
                                                            $updateB2 = Invoice::where('id', $getIn3->id)->where('status', 0)->where('usage_time', $getMinUs3)->update(['balance' => $currentBal2]);
                                                            $updateIB2 = Payment::where('invoice_id', $getIn3->id)->where('id', $createP1->id)->update(['invoice_balance' => $currentBal2]);
                                                            $updateCashA2 = Invoice::where('id', $getIn3->id)->where('status', 0)->where('usage_time', $getMinUs3)->update(['payment_id' => $createP1->id]);
                                                            $updateC2 = Invoice::where('user_id', $getIn3->id)->where('status', 0)->where('usage_time', $getMinUs3)->update(['mpesa_amount' => -($getI3->balance)]);
                                                            $updateUserA2 = User::where('id', $getIn3->user_id)->update(['amount' => $createP1->amount]);
                                                            $updateUserD2 = User::where('id', $getIn3->user_id)->update(['payment_date' => $createP1->date]);
                                                            $userBal1 = Invoice::where('user_id', $getIn3->user_id)->where('status', 0)->sum('balance');
                                                            $updateUserBal1 = User::where('id', $getIn3->user_id)->update(['balance' => $userBal1]);
                                                            $updateB2 = Invoice::where('id', $getI3->id)->update(['balance' => 0]);
                                                        } else {
                                                            $updateUserBal1 = User::where('id', $getUserIdentification->id)->update(['balance' => $getI3->balance]);

                                                        }
                                                    }

                                                }
                                            } else {
                                                $updateUserBal1 = User::where('id', $getUserIdentification->id)->update(['balance' => $getI2->balance]);

                                            }

                                        }

                                    }
                                } else {
                                    $updateUserBal1 = User::where('id', $getUserIdentification->id)->update(['balance' => $getI->balance]);

                                }

                            }

                        }
                    }
                }
                else {
                    $getUserIdentification = User::where('phone', $input['BillRefNumber'])->orWhere('phoneOne', $input['BillRefNumber'])->first();
                    if (!is_null($getUserIdentification)){
                        $getUser = User::find($getUserIdentification->id);
                        $chechIfEx = Mpesa::where('reference', $input['TransID'])->first();
                        if (is_null($chechIfEx)) {
                            $currentBalance = $getUser->balance - $input['TransAmount'];
                            $createPayment = Mpesa::create([
                                'reference' => $input['TransID'],
                                'originationTime' => date("d-m-Y", strtotime($dateFormat)),
                                'senderFirstName' => $input['FirstName'],
                                'senderMiddleName' => '',
                                'senderLastName' => '',
                                'senderPhoneNumber' => $input['MSISDN'],
                                'amount' => $input['TransAmount'],
                                'status' => '',
                                'system' => '',
                                'currency' => '',
                                'currentMonth' => $currentMonth,
                                'currentYear' =>$currentYear,
                            ]);
                            $getInvoice = Invoice::where('user_id', $getUser->id)->first();
                            $update = Mpesa::where('senderPhoneNumber', $getUser->phone)->update(['invoice_id' => $getInvoice->id]);
                            $updateUserAmount = User::where('id', $getUserIdentification->id)->update(['amount' => $createPayment->amount]);
                            $updateUserDate = User::where('id', $getUserIdentification->id)->update(['payment_date' => $createPayment->originationTime]);
                            $updateUserBalance = User::where('id', $getUserIdentification->id)->update(['balance' => $currentBalance]);
                        }
                    }
                }

            }
            else{
                $chechIfE = Mpesa::where('reference', $input['TransID'])->first();
                if (is_null($chechIfE)){
                    $createPay = Mpesa::create([
                        'reference' => $input['TransID'],
                        'originationTime' => date("d-m-Y", strtotime($dateFormat)),
                        'senderFirstName' => $input['TransID'],
                        'senderMiddleName' => '',
                        'senderLastName' => '',
                        'senderPhoneNumber' => $input['MSISDN'],
                        'amount' => $input['TransAmount'],
                        'status' => '',
                        'system' => '',
                        'currency' => '',
                        'currentMonth' =>$currentMonth,
                        'currentYear' =>$currentYear,
                    ]);
                }
            }
        }

    }
    public function authenticate(){

    }
    public function register(){

    }
}
