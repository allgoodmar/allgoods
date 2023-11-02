<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaticTextResource;
use App\StaticText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaticTextController extends Controller
{
    public function search(Request $request)
    {
        $locale = app()->getLocale();
        $key = $request->input('key', '');
        $staticText = StaticText::where('key', $key)->withTranslation($locale)->firstOrFail();
        return new StaticTextResource($staticText);
    }

    public function installmentInfo(Request $request)
    {
        $locale = app()->getLocale();
        $staticTexts = StaticText::where('key', 'LIKE', 'installment\_info\_%')->withTranslation($locale)->orderBy('key')->get();
        return StaticTextResource::collection($staticTexts);
    }
}
