<?php

namespace App\Http\Controllers\Voyager;

use App\User;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerUserController as BaseVoyagerUserController;

class VoyagerUserController extends BaseVoyagerUserController
{
    public function apiTokens(Request $request, User $user)
    {
        $this->checkPermissions();
        $tokens = $user->tokens;
        return Voyager::view('voyager::users.api_tokens.index', compact('user', 'tokens'));
    }

    public function apiTokensStore(Request $request, User $user)
    {
        $this->checkPermissions();
        $user->tokens()->delete();
        $token = $user->createToken('site');
        return redirect()->route('voyager.users.api_tokens', ['user' => $user->id])->with([
            'message'    => 'Токен создан',
            'alert-type' => 'success',
            'token' => $token->plainTextToken,
        ]);
    }

    public function editUser(Request $request, $user)
    {
        if (!empty($request->is_coupon_used) && !empty($request->coupon_sum)) {
            User::where("id", $user)->update([
                'is_coupon_used' => $request->is_coupon_used,
                'coupon_sum' => $request->coupon_sum
            ]);

            return redirect()->back();
        }
    }

    public function installmentInfo(Request $request, User $user)
    {
        $this->checkPermissions();
        return Voyager::view('voyager::users.installment_info.index', compact('user'));
    }

    public function installmentInfoVerify(Request $request, User $user)
    {
        $this->checkPermissions();
        $data = $request->validate([
            'installment_data_verified' => 'required|in:0,1,2',
            'installment_limit' => 'required|numeric',
        ]);

        $user->update([
            'installment_data_verified' => $data['installment_data_verified'],
            'installment_limit' => $data['installment_limit'],
        ]);

        return back()->with([
            'message'    => 'Статус обновлен',
            'alert-type' => 'success',
        ]);
    }

    private function checkPermissions()
    {
        if (!auth()->user()->hasPermission('browse_users')) {
            abort(403);
        }
    }
}
