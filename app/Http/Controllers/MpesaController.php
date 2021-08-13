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
    public function webhook(){
        //Store your client id and client secret as environment variables

//Including the kopokopo sdk

// do not hard code these values
        $options = [
            'clientId' => 'a5p1aivM46UQ_ekoLb8w_t7Owu9gQ_tgCJZz0fr14wc',
            'clientSecret' => '6YOgHVamcthVPu-8qKj0v76k0VBprBpJ2tvB5Jpo1jU',
            'apiKey' => '7SZBriQZw_tlSuZWyKVvFvI2tHRmV2ZW8SDTsQr_Vtg',
            'baseUrl' => 'https://sandbox.kopokopo.com'
        ];

        $K2 = new K2($options);
// Get one of the services
        $tokens = $K2->TokenService();

// Use the service
        $result = $tokens->getToken();
//        dd($result);
        $webhooks = $K2->Webhooks();

//To subscribe to a webhook
        $response = $webhooks->subscribe([
            'eventType' => 'buygoods_transaction_received',
            'url' => 'https://jnl.co.ke/storeWebhooks',
            'scope' => 'till',
            'scopeReference' => '526055',
            'accessToken' => '7f_4uIuio44vLR7J_M28hmqOPgK_tb6BrTZvulEAUo0',
        ]);
//        dd($response);


        //  Using Kopo Kopo Connect - https://github.com/kopokopo/k2-connect-php (Recommended)
        $webhooks = $K2->Webhooks();

        $options = [
            'location' => 'https://sandbox.kopokopo.com/api/v1/webhook_subscriptions/5e7db541-8f47-4483-9819-07836f65db56',
            'accessToken' => '7f_4uIuio44vLR7J_M28hmqOPgK_tb6BrTZvulEAUo0',
        ];
        $response = $webhooks->getStatus($options);
//        dd($response);
        $webhooks = $K2->Webhooks();

        $webhook_payload = file_get_contents('https://sandbox.kopokopo.com/api/v1/webhook_subscriptions/5e7db541-8f47-4483-9819-07836f65db56');

        // This will both validate and process the payload for you
        $response = $webhooks->webhookHandler($webhook_payload, $_SERVER['HTTP_X_KOPOKOPO_SIGNATURE']);

        dd($response);
    }
    public function storeWebhooks(Request $request){
        $store = Mpesa::create([
            'ido'=>$request->status,
        ]);
    }
}
