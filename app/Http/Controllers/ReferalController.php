<?php

namespace App\Http\Controllers;

use App\Referal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class ReferalController extends Controller
{
    public function referal($referal)
    {
        $referalData = Referal::where('name_ref', $referal)->first();
        $name = $referalData->name_ref;
        $coupon = $referalData->amount_coupon;
        setcookie($name,$coupon,time()+3600+24+30,"/");

        // dd($_COOKIE[$name]);
        return redirect()->route('register', ['name' => $name]);
    }

    public function voucher()
    {
        Session::put('voucher', 20000);
        return redirect()->route('register');
    }
}
