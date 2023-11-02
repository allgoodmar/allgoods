<?php

namespace App\Http\Controllers\Api\V2;

use App\Attribute;
use App\AttributeValue;
use App\Brand;
use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttributeResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $quantity = (int)$request->input('quantity', 30);
        if ($quantity > 120 || $quantity < 1) {
            $quantity = 30;
        }

        $locale = app()->getLocale();
        $categories = Category::active()->withTranslation($locale)->orderBy('order')->paginate($quantity)->appends($request->all());
        return CategoryResource::collection($categories);
    }

    public function show(Request $request, Category $category)
    {
        $locale = app()->getLocale();
        $category->load('translations');
        // $category->load(['children' => function($q) use ($locale) {
        //     $q->active()->orderBy('order')->withTranslation($locale);
        // }]);
        return new CategoryResource($category);
    }

    public function subcategories(Request $request, Category $category)
    {
        $locale = app()->getLocale();
        $category->load(['children' => function($q) use ($locale) {
            $q->active()->orderBy('order')->withTranslation($locale);
        }]);
        return CategoryResource::collection($category->children);
    }

    public function tree(Request $request)
    {
        $locale = app()->getLocale();
        $categories = Category::active()->whereNull('parent_id')->withTranslation($locale)->with(['children' => function($query) use ($locale) {
            $query->active()->withTranslation($locale)->with(['children' => function($query) use ($locale) {
                $query->active()->withTranslation($locale)->with(['children' => function($query) use ($locale) {
                    $query->active()->withTranslation($locale)->with('children')->orderBy('order');
                }])->orderBy('order');
            }])->orderBy('order');
        }])->orderBy('order')->get();
        return CategoryResource::collection($categories);
    }

    public function brands(Request $request, Category $category)
    {
        $brands = $category->allBrands();
        return BrandResource::collection($brands);
    }

    public function attributes(Request $request, Category $category)
    {
        $locale = app()->getLocale();
        $onlyFilter = (int)$request->input('only_filter', -1);
        if ($onlyFilter > 1) {
            $onlyFilter = 1;
        }

        $productIds = $category->products()->active()->pluck('products.id');

        $attributeValueIdsQuery = DB::table('attribute_value_product')->whereIn('attribute_value_product.product_id', $productIds)->groupBy('attribute_value_product.attribute_value_id')->leftJoin('attribute_values', 'attribute_value_product.attribute_value_id', '=', 'attribute_values.id');
        if ($onlyFilter != -1) {
            $attributeValueIdsQuery->where('attribute_values.used_for_filter', 1);
        }
        $attributeValueIds = $attributeValueIdsQuery->pluck('attribute_value_product.attribute_value_id');

        $attributeIds = DB::table('attribute_values')
            ->whereIn('attribute_values.id', $attributeValueIds->toArray())
            ->groupBy('attribute_values.attribute_id')
            ->pluck('attribute_values.attribute_id');

        $query = Attribute::whereIn('attributes.id', $attributeIds->toArray())
            ->withTranslation($locale)
            ->with(['attributeValues' => function ($q1) use ($locale, $attributeValueIds) {
                $q1->whereIn('attribute_values.id', $attributeValueIds->toArray())
                    ->withTranslation($locale);
            }]);
        if ($onlyFilter != -1) {
            $query->where('attributes.used_for_filter', 1);
        }
        $attributes = $query->get();
        return AttributeResource::collection($attributes);
    }

    public function prices(Request $request, Category $category)
    {
        $categoryPrices = [];
        $query = $category->products()->active();
        $categoryPrices['min'] = floatval($query->select('price')->min('price'));
        $categoryPrices['max'] = floatval($query->select('price')->max('price'));
        return response()->json($categoryPrices);
    }
}
