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
            'accessToken' => 'WLq26txH2gY2y4vtheCe0fWt4QWV99DoezQr-YwLwfQ',
        ]);
        dd($response);


    }
    public function getWebhooks(){
        $options = [
            'clientId' => '60gnT8vjNBq_9fT9xcX84kTZca57LeCXuJ4_e5jaUBM',
            'clientSecret' => 'e_Tqybes1UoheErqhG_eW9rLpDVyerJCTpnH5S95A_A',
            'apiKey' => '44b903d2b9eee2478f35609eae66f8f6bo2bed2d',
            'baseUrl' => 'https://app.kopokopo.com'
        ];
        $K2 = new K2($options);

        //  Using Kopo Kopo Connect - https://github.com/kopokopo/k2-connect-php (Recommended)
        $webhooks = $K2->Webhooks();

        $webhook_payload = file_get_contents('https://jnl.co.ke/storeWebhooks');

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
