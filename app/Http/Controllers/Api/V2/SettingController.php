<?php

namespace App\Http\Controllers\Api\V2;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $appUrl = config('app.url');
        $allgoSubcategoryIds = (string)Helper::setting('site.allgo_subcategory_ids', 300);
        $data = [
            'title' => Helper::setting('site.title', 300),
            'logo' => $appUrl . Voyager::image(Helper::setting('site.logo', 300)),
            'logo_light' => $appUrl . Voyager::image(Helper::setting('site.logo_light', 300)),
            'email' => Helper::setting('contact.email', 300),
            'phone' => Helper::setting('contact.phone', 300),
            'telegram' => Helper::setting('contact.telegram', 300),
            'facebook' => Helper::setting('contact.facebook', 300),
            'instagram' => Helper::setting('contact.instagram', 300),
            'youtube' => Helper::setting('contact.youtube', 300),
            'tiktok' => Helper::setting('contact.tiktok', 300),
            'allgo_main_category' => (int)Helper::setting('site.allgo_main_category_id', 300),
            'allgo_subcatgories' => $allgoSubcategoryIds ? array_map(function($value){
                return (int)trim($value);
            }, explode(',', $allgoSubcategoryIds)) : [],
        ];
        return response()->json($data);
    }
}
