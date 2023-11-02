<?php

namespace App\Http\Controllers\Api\V2;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Otp;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VerifyController extends Controller
{
    public function phone(Request $request)
    {
        $data = $this->validate($request, [
            'phone_number' => ['required', 'regex:/' . Helper::phoneNumberRegex() . '/', 'exists:users,phone_number'],
            'otp' => ['required'],
        ]);

        $user = User::where('phone_number', $data['phone_number'])->firstOrFail();

        $checkOTP = Helper::checkOTPByPhoneNumber($user->phone_number, $data['otp']);
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

        // verify user phone number
        $user->phone_number_verified_at = now();
        $user->save();

        // delete user otps
        Otp::where('phone_number', $data['phone_number'])->delete();
        $user->otps()->delete();

        return response()->json([
            'message' => __('Phone number has been verified'),
        ]);
    }
}

