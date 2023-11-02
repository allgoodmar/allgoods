<?php

namespace App\Http\Controllers\Api\V2;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'phone_number' => ['required'],
            'password' => ['required'],
            'device_name' => ['nullable'],
        ]);

        if (empty($data['device_name'])) {
            $data['device_name'] = Helper::getDeviceName();
        }

        if (!Auth::attempt($request->only(['phone_number', 'password']))) {
            return response()->json([
                'message' => __('Invalid login details'),
            ], 401);
        }

        $user = User::where('phone_number', $data['phone_number'])->firstOrFail();
        $token = $user->createToken($data['device_name'])->plainTextToken;

        return response()->json([
            'token' => $token,
        ]);
    }

    public function destroy(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => __('Logged out'),
        ]);
    }
}

