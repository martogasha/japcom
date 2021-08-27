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
            'clientId' => 'uB3gE_6pTvJzNe_jg8mbsmx5XmIxKEl2i-oifvaJrp4',
            'clientSecret' => 'n9JT6PbxN6poV623rvYUYOngyXXWhytASBbD98DJxs0',
            'apiKey' => 'vaoQShrNB_sJvdNWZVliGzc1_RFzmX8dtMEbkl4ETds',
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
        global $K2;
        global $response;

        $webhooks = $K2->Webhooks();

        $json_str = file_get_contents('https://jnl.co.ke/api/storeWebhooks');

        $response = $webhooks->webhookHandler($json_str, $_SERVER['HTTP_X_KOPOKOPO_SIGNATURE']);
        $response = json_encode($response);

        $store = Mpesa::create([
            'idno'=>$request->$response['status'],
            'topic'=>$response['status'],
        ]);
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
