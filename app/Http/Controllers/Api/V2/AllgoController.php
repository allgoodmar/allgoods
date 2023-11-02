<?php

namespace App\Http\Controllers\Api\V2;

use App\Category;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;

class AllgoController extends Controller
{
    public function index(Request $request)
    {
        $allgoCategoryIds = (string)Helper::setting('site.allgo_main_category_id', 300);
        $categoryIds = $allgoCategoryIds ? array_map(function($value){
            return (int)trim($value);
        }, explode(',', $allgoCategoryIds)) : [];
        $locale = app()->getLocale();
        $category = null;
        if ($categoryIds) {
            $category = Category::active()->where('id', $categoryIds[0])->withTranslation($locale)->first();
        }
        if (!$category) {
            abort(404);
        }
        return new CategoryResource($category);
    }

    public function categories(Request $request)
    {
        $allgoSubcategoryIds = (string)Helper::setting('site.allgo_subcategory_ids', 300);
        $categoryIds = $allgoSubcategoryIds ? array_map(function($value){
            return (int)trim($value);
        }, explode(',', $allgoSubcategoryIds)) : [];
        $locale = app()->getLocale();
        $categories = [];
        if ($categoryIds) {
            $categories = Category::active()->whereIn('id', $categoryIds)->withTranslation($locale)->orderBy('order')->get();
        }
        return CategoryResource::collection($categories);
    }
}
