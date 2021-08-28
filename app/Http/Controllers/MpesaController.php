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
        $options = [
            'clientId' => 'Y4oqKYiZbuy5jH3yTojM6sdi0MLlmey_Rkrx6bpOj1g',
            'clientSecret' => 'eeF7KX3QE9bmOWnEI4FY6zfskzsbaYp9hiMZIXRz6QY',
            'apiKey' => '7d36be1a6e076c4aca556ee07818b21b4e58bcfe',
            'baseUrl' => 'https://api.kopokopo.com'
        ];
        $K2 = new K2($options);
        global $response;

        $webhooks = $K2->Webhooks();
        $url = 'https://jnl.co.ke/api/storeWebhooks';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $body = curl_exec($ch);
        $response = $webhooks->webhookHandler($body, $_SERVER['HTTP_X_KOPOKOPO_SIGNATURE']);
        $data = json_encode($response);
        $store = Mpesa::create([
            'topic'=>$request->$data['data']['id'],
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
