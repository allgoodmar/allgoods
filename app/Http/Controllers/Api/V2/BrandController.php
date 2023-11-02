<?php

namespace App\Http\Controllers\Api\V2;

use App\Brand;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $quantity = (int)$request->input('quantity', 30);
        if ($quantity > 120 || $quantity < 1) {
            $quantity = 30;
        }

        $locale = app()->getLocale();
        $brands = Brand::active()->withTranslation($locale)->orderBy('order')->paginate($quantity)->appends($request->all());
        return BrandResource::collection($brands);
    }

    public function show(Request $request, Brand $brand)
    {
        // $locale = app()->getLocale();
        $brand->load('translations');
        return new BrandResource($brand);
    }
}
