<?php

namespace App\Http\Controllers\Api\V2;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Otp;
use App\User;
use App\Services\AmoCrmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        return new UserResource($request->user());
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => 'nullable|max:191',
            'birthday' => 'nullable|date_format:Y-m-d',
            'gender' => 'nullable|in:' . implode(',', array_keys(User::genders())),
            // 'phone_number' => 'regex:/' . Helper::phoneNumberRegex() . '/|unique:users,phone_number,' . $user->id,
        ]);

        if (isset($data['name'])) {
            $user->name = $data['name'];
        }
        if (isset($data['birthday'])) {
            $user->birthday = $data['birthday'];
        }
        if (isset($data['gender'])) {
            $user->gender = $data['gender'];
        }
        $user->save();

        return new UserResource($user);
    }

    public function passwordUpdate(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8',
        ]);
        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);
        return response()->json([
            'message' => __('Password has been updated'),
        ]);
    }

    public function imageUpdate(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        if ($user->avatar) {
            Helper::deleteImage($user, 'avatar', User::$imgSizes);
        }
        Helper::storeImage($user, 'image', 'users', User::$imgSizes, 'avatar');

        return response()->json([
            'message' => __('Image has been updated'),
        ]);
    }

    public function destroy(Request $request)
    {
        $data = $this->validate($request, [
            'otp' => ['required'],
        ]);

        $user = auth()->user();

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

        // delete user info
        Otp::where('phone_number', $user->phone_number)->delete();
        $user->otps()->delete();
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => __('Account has been deleted'),
        ]);
    }

    public function phoneNumberUpdate(Request $request)
    {
        $user = auth()->user();
        $data = $this->validate($request, [
            'phone_number' => 'required|regex:/' . Helper::phoneNumberRegex() . '/|unique:users,phone_number,' . $user->id,
            'otp' => 'required',
        ]);

        $user = auth()->user();

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

        $user->update(['phone_number' => $data['phone_number']]);
        $user->phone_number_verified_at = now();
        $user->save();

        // delete user info
        Otp::where('phone_number', $data['phone_number'])->delete();
        $user->otps()->delete();

        return response()->json([
            'message' => __('Phone number has been saved'),
        ]);
    }

    public function installmentInfoUpdate(Request $request)
    {
        $user = auth()->user();

        if ($user->isInstallmentDataPendingVerification() || $user->isInstallmentDataVerified()) {
            return response()->json([
                'message' => __('main.you_have_already_filled_in_the_data_wait_for_moderation'),
                'errors' => [
                    'card_number' => [
                        __('main.error'),
                    ],
                ],
            ], 422);
        }

        $data = $this->validate($request, [
            'card_number' => 'required|regex:/^\d{16}$/',
            'card_expiry' => 'required|regex:/^\d{4}$/',
            'passport_main_image' => [
                'required',
                Rule::exists('images', 'id')->where(function ($query) {
                    return $query->where('user_id', auth()->user()->id);
                }),
            ],
            'passport_address_image' => [
                'required',
                Rule::exists('images', 'id')->where(function ($query) {
                    return $query->where('user_id', auth()->user()->id);
                }),
            ],
            'otp' => 'required',
        ]);

        // check

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
        unset($data['otp']);

        // set status pending
        $user->installment_data_verified = 2;

        // update
        $user->update($data);

        // delete user info
        Otp::where('phone_number', $user->phone_number)->delete();
        $user->otps()->delete();

        // notify admin
        // Helper::toTelegram('Пользователь ID: ' . $user->id . ' заполнил форму активации рассрочки <a href="' . route('voyager.users.installment_info', [$user->id]) . '">Подробнее</a>', 'HTML', config('services.telegram.chat_id'));

        // WHILE SEND TO TELEGRAM CHANNEL:
        $botToken = "6020109850:AAG3CV00uRopavR-QgOnmD9SYqEsr9tV-8M";
        $chat_id = "@algdscrngfrlds";
        $smile = '👉';
        //$link = "http://extab.uz/fullpage.php?id=".$idd;
        $caption = "👤 Пользователь ID: ". $user->id .'  заполнил форму активации рассрочки '
        .PHP_EOL.'Подробнее: https://allgood.uz/admin/users/'.$user->id.'/installment-info';
        $photo = "https://c4.wallpaperflare.com/wallpaper/394/308/979/leonardo-dicaprio-leonardo-dicaprio-the-wolf-of-wall-street-jordan-belfort-wallpaper-preview.jpg";
        $bot_url = "https://api.telegram.org/bot$botToken/";
        $url = $bot_url."sendPhoto?chat_id=".$chat_id."&photo=".urlencode($photo)."&caption=".urlencode($caption);
        file_get_contents($url);

        // AMO_CRM_API begins

        $service = new AmoCrmService();
        $userName = $user->name . " заполнил форму активации рассрочки";

        $body = [
            [
                "name" => $userName,
                "created_by" => 0,
                "pipeline_id" => 47398930,
                "price" => 0,
                "custom_fields_values" => [
                    [
                        "field_id" => 840221,
                        "values" => [
                            [
                                "value" => 0
                            ]
                        ]
                    ],
                    [
                        "field_id" => 840225,
                        "values" => [
                            [
                                "value" => $userName
                            ]
                        ]
                    ]
                ],
            ]
        ];

        $body = json_decode(json_encode($body));
        $service->sendReq($body);
        // AMO_CRM_API ends

        return response()->json([
            'message' => __('Verification completed successfully!'),
            'description' => __('Пожалуйста подождите немного для того чтобы наша команда смогла подтвредить ваши документы')
        ]);
    }
}

