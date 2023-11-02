<?php

namespace App\Http\Controllers;

use App\AtmosApi;
use App\AtmosCard;
use App\Services\AmoCrmService;
use App\Helpers\Helper;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Throwable;

class AtmosScoringController extends Controller
{
    private $token;
    public $my_card_number;
    public $my_card_number_expiry;
    public $otp;

    public $base64test = "VjFNdXlENUtzbzdPXzhCUTRSc3pFdl9ZNXNRYTpUUVlXd25zWHdUNF9CQWREel85SVlFSVFSRE1h";
    public $base64 = "Rl9HMGhucVFPenhWaG1wMUZOMXh4SnBxVGYwYTp2b0FmQ0plUm42Rjc1dVZSRXQzeWN6TFNDNmNh";
    public $access_token1 = "85029c52-fa3f-3847-80ac-6e7f5d17105e";
    public $access_token2 = "5f933e17-24ad-347e-b43f-a121beb407a5";
    public $access_token3 = "5bb82054-b513-3814-b5b1-f6733e052a61";
    public $access_token4 = "44a3810f-f60d-3eb3-a79b-baa25723cd1b";

    public function __construct(Request $request)
    {
        if (!empty($request->otp)) {
            $this->otp = $request->otp;
        }

        $this->my_card_number = $request->card_number;
        $this->my_card_number_expiry = $request->expiry;
    }

    public function getToken(Request $request)
    {
        $card = $request->card_number;
        $result = substr($card, 0, 4);
        if ($result != "8600" && $result != "9860") {
            return redirect()->route('home');  
        }
        return $this->token = $this->getBearerToken($request);
    }

    public function getBearerToken(Request $request)
    {   
        $key = "V1MuyD5Kso7O_8BQ4RszEv_Y5sQa";
        $secret = "TQYWwnsXwT4_BAdDz_9IYEIQRDMa";
        /*
        $key = $this->base64_url_encode($key);
        $secret = $this->base64_url_encode($secret);
        $base64 = $key . ":" . $secret;
        */

        // GET BEARER ACCESS TOKEN
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic ' . $this->base64,
            'Host' => 'api.paymo.uz',
            'Content-Length' => '29'
        ];

        try {

            $dataAtmos = Http::withHeaders($headers)->post('https://api.paymo.uz/token?grant_type=client_credentials');
            // $arr = $dataAtmos->getBody()->getContents();
            $resultOfBearerToken = $dataAtmos->body();
            $resultOfBearerToken = json_decode($resultOfBearerToken);
            $my_access_token = $resultOfBearerToken->access_token;
            $check = AtmosApi::orderBy('id', 'DESC')->limit(1)->first();
            // $checkAll = AtmosApi::orderBy('id', 'DESC')->get();
            if (empty($check)) {
                AtmosApi::create([
                    'access_token' => $resultOfBearerToken->access_token,
                    'refresh_token' => null,
                ]);
            }
            
            if (isset($resultOfBearerToken->status) && $resultOfBearerToken->status == 400) {
                return $resultOfBearerToken = $this->refreshToken();
            }

        } catch (Throwable $e) {
            dd("catched while getting token, maybe expired");
            Log::debug($e);
        }
        
        return $this->createCard($request, $resultOfBearerToken, $my_access_token);
    }

    public function createCard(Request $request, $resultOfBearerToken, $my_access_token)
    {
        // CARD 
        $headersForCard = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $resultOfBearerToken->access_token,
            'Host' => 'api.paymo.uz',
            'Content-Length' => '57'
        ];
        
        $requested_card_number = str_replace(' ', '', $request->card_number);
        $requested_expiry_year_to_month = substr($request->expiry, strpos($request->expiry, "/") + 1);
        $requested_expiry_month_to_year = strtok($request->expiry, '/');
        $requested_expiry = $requested_expiry_year_to_month . $requested_expiry_month_to_year;
        
        $body = [
            'card_number' => $requested_card_number,
            'expiry' => $requested_expiry
        ];
        
        // try {
            if (!empty($this->otp)) {
                $resultOfCard = '';
            }else{
                $dataCard = Http::withHeaders($headersForCard)->post('https://api.paymo.uz/partner/bind-card/create', $body);
                $resultOfCard = $dataCard->body();
                $resultOfCard = json_decode($resultOfCard);

                if (!empty($resultOfCard) && $resultOfCard->result->code == "STPIMS-ERR-133") {
                    return $this->getCardInfo($request, $resultOfCard, $my_access_token);
                }
                // dd($resultOfCard);
                Session::put("transaction_id", $resultOfCard->transaction_id);   
            }
            
            /*$atmosCard = AtmosCard::orderBy('id', 'DESC')->get();
            foreach ($atmosCard as $key => $value) {
                if ($value->phone_number != $resultOfCard->data->phone) {
                    AtmosCard::create([
                        "name" => $resultOfCard->data->card_holder,
                        "phone_number" => $resultOfCard->data->phone,
                        "card_number" => $request->card_number,
                        "expiry" => $request->expiry,
                        "limit" => null,
                        "status" => null
                    ]);
                }
            }*/
            /*
        } catch (Throwable $e) {
            Log::debug($e);
            dd("catched while getting card transaction ID");
        }*/

        if (empty($this->otp)) {
            return redirect()->back()->with(["askOtp" => "Telefonga kelgan bir martalik parolni kiriting.", "successCard" => $this->my_card_number, "successCardExpiry" => $this->my_card_number_expiry]);
        }else{
            return $this->verifyOTP($request, $resultOfCard, $resultOfBearerToken, $my_access_token);
        }
    }

    public function verifyOTP(Request $request, $resultOfCard, $resultOfBearerToken, $my_access_token)
    {
        // CARD 
        $headersForCard = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $resultOfBearerToken->access_token,
            'Host' => 'api.paymo.uz',
            'Content-Length' => '44'
        ];

        $body = [
            'transaction_id' => Session::get("transaction_id"),
            'otp' => $this->otp,
        ];

        try {
            $dataCard = Http::withHeaders($headersForCard)->post('https://api.paymo.uz/partner/bind-card/apply', $body);
            $otpOfCard = $dataCard->body();
            $otpOfCard = json_decode($otpOfCard);

            // dd($dataCard->body());
        } catch (Throwable $e) {
            dd("catched while getting card otp");
            Log::debug($e);
        }
        
        // dd($resultOfCard);
        $this->payMerchantConfirm($request, $resultOfCard, $resultOfBearerToken, $my_access_token);
        return $this->getCardInfo($request, $otpOfCard, $my_access_token);
    }

    public function payMerchantConfirm(Request $request, $resultOfCard, $resultOfBearerToken, $my_access_token)
    {
        // CARD 
        $headersForCard = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $resultOfBearerToken->access_token,
            'Host' => 'partner.paymo.uz',
            'Content-Length' => '63'
        ];

        $body = [
            'transaction_id' => Session::get("transaction_id"),
            'otp' => $this->otp,
            'store_id' => 981,
        ];

        try {
            $dataCard = Http::withHeaders($headersForCard)->post('https://partner.paymo.uz/merchant/pay/confirm', $body);
            $confirmPayment = $dataCard->body();
            $confirmPayment = json_decode($confirmPayment);

            dd($confirmPayment);

            // dd($dataCard->body());
        } catch (Throwable $e) {
            dd("catched while getting confirmPayment");
            Log::debug($e);
        }
    }

    public function getCardInfo(Request $request, $otpOfCard, $my_access_token)
    {
        $check = AtmosApi::orderBy('id', 'DESC')->limit(1)->first();
        $card_token = $otpOfCard->data->card_token;
        // CARD 
        $headersForCardInfo = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $my_access_token,
            'Host' => 'api.paymo.uz',
        ];
        
        try {
            $cardInfo = Http::withHeaders($headersForCardInfo)->get('https://api.paymo.uz/scoring/score/by-token/'.$card_token);
            $arr = $cardInfo->getBody()->getContents();
            $resultOfCardInfo = $cardInfo->body();
            $resultOfCardInfo = json_decode($resultOfCardInfo);
        } catch (Throwable $e) {
            Log::debug($e);
            dd("catched while getting card info");
        }

        $data = $resultOfCardInfo->credit;
        $sum = array_sum((array)$data);
        $pr = $sum / 4;

        $atmosCardFirst = AtmosCard::orderBy('id', 'DESC')->limit(1)->first();
        if (!empty($atmosCardFirst)) {
            AtmosCard::where("phone", $atmosCardFirst->phone)->update([
                "limit" => Helper::formatPrice($pr)
            ]);
        }
        $atmosAmoCRM = AtmosCard::orderBy('id', 'DESC')->limit(1)->first();

        
        // dd($resultOfCardInfo);
        return $this->redirect($pr);

        /* Complex calculation - TEST TEST TEST
        // unset($data["results"]);
        // unset($data["bank"]);
        // unset($data["phone"]);
        // unset($data["pan"]);
        // unset($data["holder_name"]);

        $calculateYes = [];
        $calculateNo = [];

        foreach ($data as $key => $value) {
            if ($request->month == "3") {
                $percent = 15;
            }elseif ($request->month == "6") {
                $percent = 25;
            }elseif ($request->month == "9") {
                $percent = 32;
            }elseif ($request->month == "12") {
                $percent = 38;
            }else{
                dd("Percent undefined");
            }


            $price = $request->price;
            $onPrice = ($price * $percent) / 100;
            $price = $price + $onPrice;
            $price = $price / $request->month;
            $price = $price * 3.33;
            
            if ($price < $value) {
                $calculateYes[] = ['yes'];
            }else{
                $calculateNo[] = ['no'];
            }

            echo "<b> Plastikda bo'lishi kerak: </b> <u>" . (int)$price . "</u> so'm <br>";
            echo "<b> Plastikda bor: </b> <u>" . (int)$value . "</u> so'm <hr>";
        }
        $countYes = count($calculateYes);
        $countNo = count($calculateNo);

        if ($request->month == "3") {
            if ($countYes >= 2) {
               $answer = "Yes";
            }else{
               $answer = "No";
            }
        }elseif ($request->month == "6") {
            if ($countYes >= 4) {
                $answer = "Yes";
             }else{
                $answer = "No";
             }
        }elseif ($request->month == "9") {
            if ($countYes >= 6) {
                $answer = "Yes";
             }else{
                $answer = "No";
             }
        }elseif ($request->month == "12") {
            if ($countYes >= 8) {
                $answer = "Yes";
             }else{
                $answer = "No";
             }
        }*/
        // Oy hisobiga
        // $sum = array_sum($data);
        // $pr = $sum / 12;
        // $pr = $pr / 3.33;
        // $pr = $pr * $request->month;
    }

    public function refreshToken($first_param = null)
    {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic ' . $this->base64,
            'Host' => 'api.paymo.uz',
            'Content-Length' => '29'
        ];

        $check = AtmosApi::orderBy('id', 'DESC')->limit(1)->first();

        try {

            $dataAtmos = Http::withHeaders($headers)->post('https://api.paymo.uz/token?grant_type=client_credentials&refresh_token='.$check->access_token.'');
            // $arr = $dataAtmos->getBody()->getContents();
            $resultOfBearerToken = $dataAtmos->body();
            $resultOfBearerToken = json_decode($resultOfBearerToken);

            if (!empty($check->id)) {
                AtmosApi::where('id', $check->id)->update([
                    'access_token' => $resultOfBearerToken->access_token,
                    'refresh_token' => $check->access_token,
                ]);
            }
        } catch (Throwable $e) {
            dd("catched while getting token, maybe expired");
            Log::debug($e);
        }
        return $resultOfBearerToken;

    }

    private function base64_url_encode($input) {
        return trim(strtr(base64_encode($input), '+/', '-_'), '=');
    }

    public function redirect($limit = null)
    {
        $limit = substr($limit, 0, -2);
        return redirect()->back()->with(["successPrice" => $limit, "successCard" => $this->my_card_number, "successCardExpiry" => $this->my_card_number_expiry]);
    }
}
