<?php

namespace App\Http\Controllers\Api\V2;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Otp;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OtpController extends Controller
{
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            // 'phone_number' => ['required', 'regex:/' . Helper::phoneNumberRegex() . '/', 'exists:users,phone_number'],
            'phone_number' => ['required', 'regex:/' . Helper::phoneNumberRegex() . '/'],
        ]);

        $user = auth()->check() ? auth()->user() : null;
        $verifyCode = config('app.env') == 'production' ? mt_rand(100000, 999999) : 123456;
        // $otp = $user->otps()->create([
        //     'content' => Hash::make($verifyCode),
        // ]);
        $createData = [
            'phone_number' => $data['phone_number'],
            'content' => Hash::make($verifyCode),
        ];
        if ($user) {
            $createData['otpable_id'] = $user->id;
            $createData['otpable_type'] = User::class;
        } else {
            $createData['otpable_id'] = 0;
            $createData['otpable_type'] = '';
        }
        $otp = Otp::create($createData);
        // send sms
        Helper::sendSMS('otp' . $otp->id, $data['phone_number'], __('main.verify_code_sms_template', ['sitename' => config('app.name'), 'code' => $verifyCode]));

        return response()->json([
            'message' => __('Verification code has been sent'),
        ]);
    }

    public function check(Request $request)
    {
        $data = $this->validate($request, [
            'phone_number' => ['required', 'regex:/' . Helper::phoneNumberRegex() . '/'],
            'otp' => ['required'],
        ]);

        $checkOTP = Helper::checkOTPByPhoneNumber($data['phone_number'], $data['otp']);
        if (!$checkOTP['success']) {
            return response()->json([
                'message' => __('main.error'),
                'errors' => [
                    'otp' => [
                        $checkOTP['error'],
                    ],
                ],
            ], 422);
        }

        return response()->json([
            'message' => __('OTP is valid'),
        ]);
    }
}

