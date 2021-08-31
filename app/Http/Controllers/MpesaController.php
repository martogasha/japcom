<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use App\Models\Invoice;
use App\Models\Mpesa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kopokopo\SDK\K2;

class MpesaController extends Controller
{
    public function index(){
        $mpesas = Mpesa::all();
        return view('admin.mpesa',[
            'mpesas'=>$mpesas
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
            $input = $request->json()->all();
            $getUserIdentification = User::where('phone',$input['event']['resource']['sender_phone_number'])->first();
        $getMinUsage = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->min('usage_time');
        $getInvoice = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->where('usage_time',$getMinUsage)->first();
        $currentBalance = $getInvoice->balance - $input['event']['resource']['amount'];
        $createPayment = Mpesa::create([
            'reference'=>$input['event']['resource']['reference'],
            'originationTime'=>$input['event']['resource']['origination_time'],
            'senderFirstName'=>$input['event']['resource']['sender_first_name'],
            'senderMiddleName'=>$input['event']['resource']['sender_middle_name'],
            'senderLastName'=>$input['event']['resource']['sender_last_name'],
            'senderPhoneNumber'=>$input['event']['resource']['sender_phone_number'],
            'amount'=>$input['event']['resource']['amount'],
            'status'=>$input['event']['resource']['status'],
            'system'=>$input['event']['resource']['system'],
            'currency'=>$input['event']['resource']['currency'],
            'invoice_id'=>$getInvoice->id,

        ]);
        $updateBalance = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->where('usage_time',$getMinUsage)->update(['balance'=>$currentBalance]);
        $updateIBalance = Mpesa::where('invoice_id',$getInvoice->id)->where('id',$createPayment->id)->update(['invoice_balance'=>$currentBalance]);
        $updateCashAmount = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->where('usage_time',$getMinUsage)->update(['mpesa_id'=>$createPayment->id]);
        $updateCash = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->where('usage_time',$getMinUsage)->update(['cash_amount'=>$request->amount]);
        $updateUserAmount = User::where('id',$getUserIdentification->id)->update(['amount'=>$createPayment->amount]);
        $updateUserDate = User::where('id',$request->user_id)->update(['payment_date'=>$createPayment->originationTime]);
        $userBalance = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->sum('balance');
        $updateUserBalance = User::where('id',$getUserIdentification->id)->update(['balance'=>$userBalance]);
        $getInv = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->where('usage_time',$getMinUsage)->first();
        if ($getInv->balance==0){
            $updateBal = Invoice::where('id',$getInv->id)->update(['usage_time'=>2147483647]);
            $updateStatus = Invoice::where('id',$getInv->id)->update(['status'=>1]);
        }
        else{
            if ($getInv->balance<0){
                $updateBal = Invoice::where('id',$getInv->id)->update(['usage_time'=>2147483647]);
                $updateStatus = Invoice::where('id',$getInv->id)->update(['status'=>1]);
                $getMinUs = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->min('usage_time');
                $getIn = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->where('usage_time',$getMinUs)->first();
                $getI = Invoice::where('user_id',$getUserIdentification->id)->where('balance','<',0)->first();
                if ($getIn){
                    $currentBal = $getIn->balance + $getI->balance;
                    $createPay = Mpesa::create([
                        'invoice_id'=>$getIn->id,
                        'reference'=>$input['event']['resource']['reference'],
                        'originationTime'=>$input['event']['resource']['origination_time'],
                        'senderFirstName'=>$input['event']['resource']['sender_first_name'],
                        'senderMiddleName'=>$input['event']['resource']['sender_middle_name'],
                        'senderLastName'=>$input['event']['resource']['sender_last_name'],
                        'senderPhoneNumber'=>$input['event']['resource']['sender_phone_number'],
                        'amount'=>$getI->balance * -1,
                        'status'=>1,
                        'system'=>$input['event']['resource']['system'],
                        'currency'=>$input['event']['resource']['currency'],
                    ]);
                    $updateB = Invoice::where('id',$getIn->id)->where('status',0)->where('usage_time',$getMinUs)->update(['balance'=>$currentBal]);
                    $updateIB= Mpesa::where('invoice_id',$getIn->id)->where('id',$createPay->id)->update(['invoice_balance'=>$currentBal]);
                    $updateCashA = Invoice::where('id',$getIn->id)->where('status',0)->where('usage_time',$getMinUs)->update(['mpesa_id'=>$createPay->id]);
                    $updateC = Invoice::where('id',$getIn->id)->where('status',0)->where('usage_time',$getMinUs)->update(['mpesa_amount'=>-($getI->balance)]);
                    $updateUserA = User::where('id',$getIn->user_id)->update(['amount'=>$createPay->amount]);
                    $updateUserD = User::where('id',$getIn->user_id)->update(['payment_date'=>$createPay->originationTime]);
                    $userBal= Invoice::where('user_id',$getIn->user_id)->where('status',0)->sum('balance');
                    $updateUserBal = User::where('id',$getIn->user_id)->update(['balance'=>$userBal]);
                    $updateB = Invoice::where('id',$getI->id)->update(['balance'=>0]);
                    $getMinUs1 = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->min('usage_time');
                    $getIn1 = Invoice::where('user_id',$getUserIdentification->id)->where('status',0)->where('usage_time',$getMinUs1)->first();
                    if ($getIn1->balance==0){
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
                                $createP = Mpesa::create([
                                    'invoice_id'=>$getIn2->id,
                                    'amount'=>$getI2->balance * -1,
                                    'date'=>$input['event']['resource']['origination_time'],
                                    'reason'=>'Internet Subscription',
                                    'reference'=>$input['event']['resource']['reference'],
                                    'originationTime'=>$input['event']['resource']['origination_time'],
                                    'senderFirstName'=>$input['event']['resource']['sender_first_name'],
                                    'senderMiddleName'=>$input['event']['resource']['sender_middle_name'],
                                    'senderLastName'=>$input['event']['resource']['sender_last_name'],
                                    'senderPhoneNumber'=>$input['event']['resource']['sender_phone_number'],
                                    'status'=>1,
                                    'system'=>$input['event']['resource']['system'],
                                    'currency'=>$input['event']['resource']['currency'],
                                ]);
                                $updateB2 = Invoice::where('id',$getIn2->id)->where('status',0)->where('usage_time',$getMinUs2)->update(['balance'=>$currentBal1]);
                                $updateIB2= Mpesa::where('invoice_id',$getIn2->id)->where('id',$createP->id)->update(['invoice_balance'=>$currentBal1]);
                                $updateCashA2 = Invoice::where('id',$getIn2->id)->where('status',0)->where('usage_time',$getMinUs2)->update(['mpesa_id'=>$createP->id]);
                                $updateC2 = Invoice::where('user_id',$getIn2->id)->where('status',0)->where('usage_time',$getMinUs2)->update(['mpesa_amount'=>-($getI2->balance)]);
                                $updateUserA2 = User::where('id',$getIn2->user_id)->update(['amount'=>$createP->amount]);
                                $updateUserD2 = User::where('id',$getIn2->user_id)->update(['payment_date'=>$createP->originationTime]);
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
                                            $createP = Cash::create([
                                                'invoice_id'=>$getIn3->id,
                                                'amount'=>$getI3->balance * -1,
                                                'date'=>$input['event']['resource']['origination_time'],
                                                'reason'=>'Internet Subscription',
                                                'reference'=>$input['event']['resource']['reference'],
                                                'originationTime'=>$input['event']['resource']['origination_time'],
                                                'senderFirstName'=>$input['event']['resource']['sender_first_name'],
                                                'senderMiddleName'=>$input['event']['resource']['sender_middle_name'],
                                                'senderLastName'=>$input['event']['resource']['sender_last_name'],
                                                'senderPhoneNumber'=>$input['event']['resource']['sender_phone_number'],
                                                'status'=>1,
                                                'system'=>$input['event']['resource']['system'],
                                                'currency'=>$input['event']['resource']['currency'],
                                            ]);
                                            $updateB2 = Invoice::where('id',$getIn3->id)->where('status',0)->where('usage_time',$getMinUs3)->update(['balance'=>$currentBal2]);
                                            $updateIB2= Cash::where('invoice_id',$getIn3->id)->where('id',$createP->id)->update(['invoice_balance'=>$currentBal2]);
                                            $updateCashA2 = Invoice::where('id',$getIn3->id)->where('status',0)->where('usage_time',$getMinUs3)->update(['cash_id'=>$createP->id]);
                                            $updateC2 = Invoice::where('user_id',$getIn3->id)->where('status',0)->where('usage_time',$getMinUs3)->update(['cash_amount'=>-($getI3->balance)]);
                                            $updateUserA2 = User::where('id',$getIn3->user_id)->update(['amount'=>$createP->amount]);
                                            $updateUserD2 = User::where('id',$getIn3->user_id)->update(['payment_date'=>$request->payment_date]);
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
    public function authenticate(){
        global $K2;
        global $response;

        $webhooks = $K2->Webhooks();

        $json_str = file_get_contents('https://jnl.co.ke/api/storeWebhooks');

        $response = $webhooks->webhookHandler($json_str, $_SERVER['vaoQShrNB_sJvdNWZVliGzc1_RFzmX8dtMEbkl4ETds']);
        dd($response);
    }
}
