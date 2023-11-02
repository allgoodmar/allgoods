<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AlifshopAzoService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AlifshopAzoController extends Controller
{
    public function clientsCheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|regex:/^998\d{9}$/',
            'order_total' => 'required|numeric',
        ]);

        $response = [
            'message' => '',
        ];

        if ($validator->fails()) {
            $response['message'] = __('main.error');
            $response['errors'] = $validator->errors();
            return response()->json($response, 422);
        } else {
            $service = new AlifshopAzoService();
            // client check
            $result = $service->clientsCheck($request->phone_number);
            if ($result !== false) {
                $code = $result->getStatusCode();
                $message = $result->getBody()->getContents();
                if ($code == 400) {
                    $response['message'] = __('main.alifshop_no_account_error');
                    return response()->json($response, 404);
                } elseif ($code == 200) {
                    // client limit check
                    // $result = $service->clientsLimit($request->phone_number);
                    // $code = $result->getStatusCode();
                    // $body = $result->getBody()->getContents();
                    // $json = json_decode($body, true);
                    // if ($code == 404) {
                    //     $response['message'] = __('main.alifshop_no_account_error');
                    //     return response()->json($response, 404);
                    // } elseif (
                    //     $code == 200 &&
                    //     ( (isset($json['current_amount']) && is_numeric($json['current_amount'])) || (isset($json['code']) && $json['code'] == 'client_has_no_limit') )
                    // ) {
                    //     // check order total and limit
                    //     $limit = (float)$json['current_amount'];
                    //     $totalTiyin = (float)$request->input('order_total', 0) * 100;
                    //     if ($totalTiyin > $limit) {
                    //         $response['message'] = __('main.alifshop_limit_error');
                    //         return response()->json($response, 460);
                    //     }

                    //     // request otp
                    //     $service->requestOTP($request->phone_number);
                    //     session()->put('alifshop_otp_sent', 1);
                    //     session()->put('alifshop_phone_number', $request->phone_number);
                    //     $response['message'] = __('main.alifshop_sms_verification_has_been_sent_to_your_phone');
                    //     return response()->json($response);
                    // }

                    // send without limit check
                    // request otp
                    $res = $service->requestOTP($request->phone_number);
                    // check result
                    if ($res === false || $res->getStatusCode() != 200) {
                        $response['message'] = __('main.sms_sending_error');
                        return response()->json($response, 400);
                    }
                    session()->put('alifshop_otp_sent', 1);
                    session()->put('alifshop_phone_number', $request->phone_number);
                    $response['message'] = __('main.alifshop_sms_verification_has_been_sent_to_your_phone');
                    return response()->json($response);
                }
            }
        }

        if (!$response['message']) {
            $response['message'] = __('main.error');
        }
        return response()->json($response, 400);
    }
}
