<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelperController extends Controller
{
    public function refereshCaptcha()
    {
        return captcha_src('flat');
    }
}
