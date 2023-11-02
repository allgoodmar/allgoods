<?php

namespace App\Http\Controllers\Api\V2;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        // $params = $request->all();
        // Log::info($params);

        $data = $this->validate($request, [
            'name' => ['required', 'string', 'max:191'],
            //'email' => ['required', 'string', 'email', 'max:191', 'unique:users'],
            'phone_number' => ['required', 'regex:/' . Helper::phoneNumberRegex() . '/', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'referrer_id' => ['nullable', 'exists:users,id'],
            'device_name' => ['nullable', 'max:191'],
        ]);

        // create user
        $user = User::create([
            'name' => $data['name'],
            'phone_number' => $data['phone_number'],
            'email' => uniqid() . '@' . request()->getHost(),
            'password' => Hash::make($data['password']),
            'referrer_id' => $data['referrer_id'] ?? '',
        ]);

        // create token
        if (empty($data['device_name'])) {
            $data['device_name'] = Helper::getDeviceName();
        }
        $token = $user->createToken($data['device_name'])->plainTextToken;
        return response()->json([
            'token' => $token,
        ]);

        // return new UserResource($user);
    }
}

