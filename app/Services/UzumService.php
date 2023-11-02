<?php 

namespace App\Services;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class UzumService 
{
    public $token;
    public $companyId;
    public $callBack;
    public $uzum_items;
    public $uzum_months;
    public $tariff;

    public function __construct($current_url = null, $uzum_items = null, $uzum_months = null, $tariff = null)
    {
        $this->token = "71da0f8b965cedf65569baaf85ba6889";
        $this->companyId = "666666";
        $this->callBack = $current_url ?? null;
        $this->uzum_items = $uzum_items ?? array();
        $this->uzum_months = $uzum_months ?? null;
        $this->tariff = $tariff ?? null;
    }

    public function checkStatus($phone_number)
    {   
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ])
        ->post('https://tori.paymart.uz/api/v3/uzum/buyer/check-status', [
            "phone" => $phone_number
        ]);
    
        $responseData = $response->json();
        $data = $responseData['data'];
        $companyId = urlencode($this->companyId);
        $callBack = urlencode($this->callBack);
        $baseUrl = $data['webview'];
        $callback_url = $baseUrl . "&companyId=".$companyId."&callback=$callBack";

        if ($data['status'] == 0) {
            $checkUzumResponse = [
                'message' => "callback_for_status_zero",
                "callback" => $callback_url
            ];
            return $checkUzumResponse;
        }else{
            if ($data['status'] == 5 || $data['status'] == 4) {
                if (empty($uzum_months)) {

                    $uzum_items_less_conf = $this->uzum_items;
                    unset($uzum_items_less_conf['category']);
                    unset($uzum_items_less_conf['name']);

                    $response2 = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->token
                    ])
                    ->post('https://tori.paymart.uz/api/v3/mfo/calculate', [
                        "user_id" => $data['buyer_id'],
                        'products' => [
                            $uzum_items_less_conf
                        ]
                    ]);
        
                    
                    // tariff section starts
                    $this->uzum_items['imei'] = "213421341234134";
                    $this->uzum_items['unit_id'] = 1;

                    $response3 = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->token
                    ])
                    ->post('https://tori.paymart.uz/api/v3/mfo/order', [
                        "user_id" => $data['buyer_id'],
                        "period" => $this->tariff,
                        "callback" => "uzumnasiya.uz",
                        'products' => [
                            $this->uzum_items
                        ]
                    ]);
        
                    $responseData3 = $response3->json();
                    $data3 = $responseData3['data'];
                    
                    if ($responseData3['status'] != "error") {
                        /*
                        $response4 = Http::withHeaders([
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . $this->token
                        ])->get('https://authtest.uzumnasiya.uz', [
                            'contractId' => $data3['paymart_client']['contract_id'],
                            'callback' => $callback_url,
                            'isMocked' => 'true',
                        ]);*/

                        $checkUzumResponse = [
                            'message' => "callback_for_order",
                            "callback" => $callback_url,
                            "uzum_token" => $this->token,
                            "contract_id" => $data3['paymart_client']['contract_id'],
                            "uzum_contract" => $data3['client_act_pdf'],
                        ];

                        return $checkUzumResponse;
                    }
                    dd($responseData3);
                    // return back()->with('successUzumData', $data2)->with('successUzumPhone', $phone_number);
                } else {
                    dd("Uzum months");
                }
            } else {
                dd("error: check status id - ".$data['status']);
            }
        }

        if ($response->successful()) {
            
            dd($data);
        }else{
            $errorResponse = $response->json();
            dd($errorResponse);
        }
    }
}