<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use App\Models\Invoice;
use App\Models\Money;
use App\Models\Mpesa;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kopokopo\SDK\K2;

class MpesaController extends Controller
{
    public function index(){
        $mpesas = Mpesa::where('status','!=',3)->get();
        $total = Mpesa::where('status','!=',3)->sum('amount');
        return view('admin.mpesa',[
            'mpesas'=>$mpesas,
            'total'=>$total,
        ]);
    }
    public function subscribe(){
        $options = [
            'clientId' => 'Y4oqKYiZbuy5jH3yTojM6sdi0MLlmey_Rkrx6bpOj1g',
            'clientSecret' => 'eeF7KX3QE9bmOWnEI4FY6zfskzsbaYp9hiMZIXRz6QY',
            'apiKey' => '7d36be1a6e076c4aca556ee07818b21b4e58bcfe',
            'baseUrl' => 'https://api.kopokopo.com'
        ];
        $K2 = new K2($options);
        $tokens = $K2->TokenService();
        $result = $tokens->getToken();
        $access = $result['data'];
        $accessToken = $access['accessToken'];
        $webhooks = $K2->Webhooks();
        $response = $webhooks->subscribe([
            'eventType' => 'buygoods_transaction_received',
            'url' => 'https://jnl.co.ke/api/storeWebhooks',
            'scope' => 'till',
            'scopeReference' => '526055',
            'accessToken' => $accessToken,
        ]);
        $location = $response['location'];
        $stk = $K2->StkService();
        $options = [
            'location' => $location,
            'accessToken' => $accessToken,
        ];
        $response = $stk->getStatus($options);
        dd($response);
    }

    public function storeWebhooks(Request $request){
            $duplicate = $request->json()->all();
            $dub = array($duplicate);
        $input = array_unique($dub);
        $dateFormat = $input[0]['event']['resource']['origination_time'];
        $duplicatePayments = Money::create([
            'reference'=>$input[0]['event']['resource']['reference'],
            'originationTime'=> date("d-m-Y", strtotime($dateFormat)),
            'senderFirstName'=>$input[0]['event']['resource']['sender_first_name'],
            'senderMiddleName'=>$input[0]['event']['resource']['sender_middle_name'],
            'senderLastName'=>$input[0]['event']['resource']['sender_last_name'],
            'senderPhoneNumber'=>$input[0]['event']['resource']['sender_phone_number'],
            'amount'=>$input[0]['event']['resource']['amount'],
            'status'=>$input[0]['event']['resource']['status'],
            'system'=>$input[0]['event']['resource']['system'],
            'currency'=>$input[0]['event']['resource']['currency'],
        ]);
        $duplicatePayments = Money::all();
        $getCollections = collect($duplicatePayments);
        $uniquePayments = $getCollections->unique();
        $uniquePayments->all();

            foreach ($uniquePayments as $uniquePayment){
                    $getUserIdentification = User::where('phone', $uniquePayment->senderPhoneNumber)->first();
                    $getInvoice = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->first();
                    if ($getInvoice){
                        $currentBalance = $getInvoice->balance - $uniquePayment->amount;
                        $createPayment = Mpesa::create([
                            'reference'=>$uniquePayment->reference,
                            'originationTime'=> $uniquePayment->originationTime,
                            'senderFirstName'=>$uniquePayment->senderFirstName,
                            'senderMiddleName'=>$uniquePayment->senderMiddleName,
                            'senderLastName'=>$uniquePayment->senderLastName,
                            'senderPhoneNumber'=>$uniquePayment->senderPhoneNumber,
                            'amount'=>$uniquePayment->amount,
                            'status'=>$uniquePayment->status,
                            'system'=>$uniquePayment->system,
                            'currency'=>$uniquePayment->currency,
                            'invoice_id'=>$getInvoice->id,

                        ]);
                        $createPay = Payment::create([
                            'user_id'=>$getUserIdentification->id,
                            'invoice_id'=>$getInvoice->id,
                            'reference'=>$uniquePayment->reference,
                            'date'=>$uniquePayment->originationTime,
                            'amount'=>$createPayment->amount,
                            'status'=>1,
                            'payment_method'=>'Mpesa',

                        ]);
                        $updateInvoiceBalance = Invoice::where('id', $getInvoice->id)->update(['balance' => $currentBalance]);
                        $updateInvoicePaymentId = Invoice::where('id', $getInvoice->id)->update(['payment_id' => $createPay->id]);
                        $updateInvoiceMId = Invoice::where('id', $getInvoice->id)->update(['mpesa_id' => $createPayment->id]);
                        $updateInvoiceMAmount = Invoice::where('id', $getInvoice->id)->update(['mpesa_amount' => $createPayment->amount]);
                        $updateIBalance = Payment::where('id', $createPay->id)->update(['invoice_balance' => $currentBalance]);
                        $updateUserAmount = User::where('id', $getUserIdentification->id)->update(['amount' => $createPayment->amount]);
                        $updateUserDate = User::where('id', $getUserIdentification->id)->update(['payment_date' => $createPay->date]);
                        $updateUserBalance = User::where('id', $getUserIdentification->id)->update(['balance' => $currentBalance]);
                        $getInv = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->first();
                        if ($getInv->balance==0){
                            $updateBal = Invoice::where('id',$getInv->id)->update(['usage_time'=>2147483647]);
                            $updateStatus = Invoice::where('id',$getInv->id)->update(['status'=>1]);
                        }
                        else{
                            if ($getInv->balance<0){
                                $updateBal = Invoice::where('id',$getInv->id)->update(['usage_time'=>2147483647]);
                                $updateStatus = Invoice::where('id',$getInv->id)->update(['status'=>1]);
                                $getIn = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->first();
                                $getI = Invoice::where('user_id',$getUserIdentification->id)->where('balance','<',0)->first();
                                if ($getIn){
                                    $currentBal = $getIn->balance + $getI->balance;
                                    $createPay1 = Payment::create([
                                        'user_id'=>$getUserIdentification->id,
                                        'invoice_id'=>$getIn->id,
                                        'reference'=>$input[0]['event']['resource']['reference'],
                                        'date'=>date("d-m-Y", strtotime($dateFormat)),
                                        'amount'=>$getI->balance * -1,
                                        'status'=>1,
                                        'payment_method'=>'Mpesa',

                                    ]);
                                    $updateB = Invoice::where('id',$getIn->id)->where('status',0)->update(['balance'=>$currentBal]);
                                    $updateIB= Payment::where('invoice_id',$getIn->id)->where('id',$createPay1->id)->update(['invoice_balance'=>$currentBal]);
                                    $updateInvoicePayment = Invoice::where('id',$getIn->id)->where('status',0)->update(['payment_id'=>$createPay1->id]);
                                    $updateC = Invoice::where('id',$getIn->id)->where('status',0)->update(['mpesa_amount'=>-($getI->balance)]);
                                    $updateUserA = User::where('id',$getIn->user_id)->update(['amount'=>$createPay1->amount]);
                                    $updateUserD = User::where('id',$getIn->user_id)->update(['payment_date'=>$createPay1->date]);
                                    $userBal= Invoice::where('user_id',$getIn->user_id)->where('status',0)->sum('balance');
                                    $updateUserBal = User::where('id',$getIn->user_id)->update(['balance'=>$userBal]);
                                    $updateB = Invoice::where('id',$getI->id)->update(['balance'=>0]);
                                    $getMinUs1 = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->min('usage_time');
                                    $getIn1 = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->where('usage_time',$getMinUs1)->first();
                                    if ($getIn1->balance==0){
                                        $updateCashA = Invoice::where('id',$getIn->id)->where('status',0)->update(['mpesa_id'=>$createPay->id]);
                                        $updateBal = Invoice::where('id',$getIn1->id)->update(['usage_time'=>2147483647]);
                                        $updateStatus = Invoice::where('id',$getIn1->id)->update(['status'=>1]);
                                    }
                                    else{
                                        if ($getIn1->balance<0){
                                            $updateBal = Invoice::where('id',$getIn1->id)->update(['usage_time'=>2147483647]);
                                            $updateStatus = Invoice::where('id',$getIn1->id)->update(['status'=>1]);
                                            $getMinUs2 = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->min('usage_time');
                                            $getIn2 = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->where('usage_time',$getMinUs2)->first();
                                            $getI2 = Invoice::where('user_id',$getUserIdentification->id)->where('balance','<',0)->first();
                                            if ($getIn2){
                                                $currentBal1 = $getIn2->balance + $getI2->balance;
                                                $createP = Payment::create([
                                                    'user_id'=>$getUserIdentification->id,
                                                    'invoice_id'=>$getIn2->id,
                                                    'reference'=>$input[0]['event']['resource']['reference'],
                                                    'date'=>date("d-m-Y", strtotime($dateFormat)),
                                                    'amount'=>$getI2->balance * -1,
                                                    'status'=>1,
                                                    'payment_method'=>'Mpesa',
                                                ]);
                                                $updateB2 = Invoice::where('id',$getIn2->id)->where('status',0)->where('usage_time',$getMinUs2)->update(['balance'=>$currentBal1]);
                                                $updateIB2= Payment::where('invoice_id',$getIn2->id)->where('id',$createP->id)->update(['invoice_balance'=>$currentBal1]);
                                                $updateC2 = Invoice::where('user_id',$getIn2->id)->where('status',0)->where('usage_time',$getMinUs2)->update(['mpesa_amount'=>-($getI2->balance)]);
                                                $updatePaymentId = Invoice::where('user_id',$getIn2->id)->where('status',0)->where('usage_time',$getMinUs2)->update(['payment_id'=>$createP->id]);
                                                $updateUserA2 = User::where('id',$getIn2->user_id)->update(['amount'=>$createP->amount]);
                                                $updateUserD2 = User::where('id',$getIn2->user_id)->update(['payment_date'=>$createP->date]);
                                                $userBal1= Invoice::where('user_id',$getIn2->user_id)->where('status',0)->sum('balance');
                                                $updateUserBal1 = User::where('id',$getIn2->user_id)->update(['balance'=>$userBal1]);
                                                $updateB2 = Invoice::where('id',$getI2->id)->update(['balance'=>0]);
                                                $getMinUs2 = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->min('usage_time');
                                                $getIn2 = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->where('usage_time',$getMinUs2)->first();
                                                if ($getIn2->balance==0){
                                                    $updateBal = Invoice::where('id',$getIn2->id)->update(['usage_time'=>2147483647]);
                                                    $updateStatus = Invoice::where('id',$getIn2->id)->update(['status'=>1]);
                                                }
                                                else{
                                                    if ($getIn2->balance<0){
                                                        $updateBal = Invoice::where('id',$getIn2->id)->update(['usage_time'=>2147483647]);
                                                        $updateStatus = Invoice::where('id',$getIn2->id)->update(['status'=>1]);
                                                        $getMinUs3 = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->min('usage_time');
                                                        $getIn3 = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->where('usage_time',$getMinUs3)->first();
                                                        $getI3 = Invoice::where('user_id',$getUserIdentification->id)->where('balance','<',0)->first();
                                                        if ($getIn3){
                                                            $currentBal2 = $getIn3->balance + $getI3->balance;
                                                            $createP1 = Payment::create([
                                                                'invoice_id'=>$getIn3->id,
                                                                'user_id'=>$getUserIdentification->id,
                                                                'reference'=>$input[0]['event']['resource']['reference'],
                                                                'date'=>date("d-m-Y", strtotime($dateFormat)),
                                                                'amount'=>$getI3->balance * -1,
                                                                'status'=>1,
                                                                'payment_method'=>'Mpesa',
                                                            ]);
                                                            $updateB2 = Invoice::where('id',$getIn3->id)->where('status',0)->where('usage_time',$getMinUs3)->update(['balance'=>$currentBal2]);
                                                            $updateIB2= Payment::where('invoice_id',$getIn3->id)->where('id',$createP1->id)->update(['invoice_balance'=>$currentBal2]);
                                                            $updateCashA2 = Invoice::where('id',$getIn3->id)->where('status',0)->where('usage_time',$getMinUs3)->update(['payment_id'=>$createP1->id]);
                                                            $updateC2 = Invoice::where('user_id',$getIn3->id)->where('status',0)->where('usage_time',$getMinUs3)->update(['mpesa_amount'=>-($getI3->balance)]);
                                                            $updateUserA2 = User::where('id',$getIn3->user_id)->update(['amount'=>$createP1->amount]);
                                                            $updateUserD2 = User::where('id',$getIn3->user_id)->update(['payment_date'=>$createP1->date]);
                                                            $userBal1= Invoice::where('user_id',$getIn3->user_id)->where('status',0)->sum('balance');
                                                            $updateUserBal1 = User::where('id',$getIn3->user_id)->update(['balance'=>$userBal1]);
                                                            $updateB2 = Invoice::where('id',$getI3->id)->update(['balance'=>0]);
                                                        }
                                                        else{
                                                            $updateUserBal1 = User::where('id',$getUserIdentification->id)->update(['balance'=>$getI3->balance]);

                                                        }
                                                    }

                                                }
                                            }
                                            else{
                                                $updateUserBal1 = User::where('id',$getUserIdentification->id)->update(['balance'=>$getI2->balance]);

                                            }

                                        }

                                    }
                                }
                                else{
                                    $updateUserBal1 = User::where('id',$getUserIdentification->id)->update(['balance'=>$getI->balance]);

                                }

                            }

                        }
                    }
                    else{
                        $getUser = User::find($getUserIdentification->id);
                        if ($getUser){
                            $currentBalance = $getUser->balance - $uniquePayment->amount;
                            $createPayment = Mpesa::create([
                                'reference'=>$uniquePayment->reference,
                                'originationTime'=> $uniquePayment->originationTime,
                                'senderFirstName'=>$uniquePayment->senderFirstName,
                                'senderMiddleName'=>$uniquePayment->senderMiddleName,
                                'senderLastName'=>$uniquePayment->senderLastName,
                                'senderPhoneNumber'=>$uniquePayment->senderPhoneNumber,
                                'amount'=>$uniquePayment->amount,
                                'status'=>$uniquePayment->status,
                                'system'=>$uniquePayment->system,
                                'currency'=>$uniquePayment->currency,

                            ]);
                            $updateUserAmount = User::where('id',$getUserIdentification->id)->update(['amount'=>$createPayment->amount]);
                            $updateUserDate = User::where('id',$getUserIdentification->id)->update(['payment_date'=>$createPayment->originationTime]);
                            $updateUserBalance = User::where('id',$getUserIdentification->id)->update(['balance'=>$currentBalance]);
                        }
                        else{
                            $createPayment = Mpesa::create([
                                'reference'=>$uniquePayment->reference,
                                'originationTime'=> $uniquePayment->originationTime,
                                'senderFirstName'=>$uniquePayment->senderFirstName,
                                'senderMiddleName'=>$uniquePayment->senderMiddleName,
                                'senderLastName'=>$uniquePayment->senderLastName,
                                'senderPhoneNumber'=>$uniquePayment->senderPhoneNumber,
                                'amount'=>$uniquePayment->amount,
                                'status'=>$uniquePayment->status,
                                'system'=>$uniquePayment->system,
                                'currency'=>$uniquePayment->currency,
                            ]);
                        }

                    }
                    $deteleDuplicate = Money::where('reference', $uniquePayment->reference)->delete();

            }




    }
    public function authenticate(){
        global $K2;
        global $response;

        $webhooks = $K2->Webhooks();

        $json_str = file_get_contents('https://jnl.co.ke/api/storeWebhooks');

        $response = $webhooks->webhookHandler($json_str, $_SERVER['vaoQShrNB_sJvdNWZVliGzc1_RFzmX8dtMEbkl4ETds']);
        dd($response);
    }
}
