<?php

namespace App\Http\Controllers\Api\V2;

use App\Banner;
use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(Banner::types())),
            'category_id' => 'nullable|exists:categories,id',
        ]);
        $locale = app()->getLocale();
        $query = Banner::active()->withTranslation($locale)->where('type', $data['type'])->latest()->take(10);
        if (!empty($data['category_id'])) {
            $query->where('category_id', $data['category_id']);
        }
        $banners = $query->get();
        return BannerResource::collection($banners);
    }
}
