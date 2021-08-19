<?php

namespace App\Http\Controllers;

use App\Models\Mpesa;
use Illuminate\Http\Request;
use Kopokopo\SDK\K2;

class MpesaController extends Controller
{
    public function index(){
        return view('admin.mpesa');
    }
    public function subscribe(){
        $options = [
            'clientId' => 'a5p1aivM46UQ_ekoLb8w_t7Owu9gQ_tgCJZz0fr14wc',
            'clientSecret' => '6YOgHVamcthVPu-8qKj0v76k0VBprBpJ2tvB5Jpo1jU',
            'apiKey' => '7SZBriQZw_tlSuZWyKVvFvI2tHRmV2ZW8SDTsQr_Vtg',
            'baseUrl' => 'https://sandbox.kopokopo.com'
        ];
        $K2 = new K2($options);
        $tokens = $K2->TokenService();
        $result = $tokens->getToken();
        $access = $result['data'];
        $accessToken = $access['accessToken'];
        $webhooks = $K2->Webhooks();
        $response = $webhooks->subscribe([
            'eventType' => 'buygoods_transaction_received',
            'url' => 'https://jnl.co.ke/storeWebhooks',
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
        global $K2;
        global $response;

        $webhooks = $K2->Webhooks();

        $json_str = file_get_contents('https://sandbox.kopokopo.com/api/v1/webhook_subscriptions/b2a26855-35de-45a6-a144-31246358fd4a');

        $response = $webhooks->webhookHandler($json_str, $_SERVER['HTTP_X_KOPOKOPO_SIGNATURE']);
        $mpsa = $response['data'];
        $store = Mpesa::create([
            'idno'=>$request->$response['status'],
            'topic'=>$request->$mpsa['id'],
        ]);
    }
}
