<?php

namespace App\Http\Controllers\Api\V2;

use App\Attribute;
use App\Brand;
use App\Category;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttributeResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Product;
use App\Search;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'search' => 'required|string|min:3',
            'type' => 'nullable|in:categories,brands,products',
        ]);
        $locale = app()->getLocale();
        $type = !empty($data['type']) ? $data['type'] : 'products';
        $typeClassName = Product::class;
        if ($type == 'categories') {
            $typeClassName = Category::class;
        } elseif ($type == 'brands') {
            $typeClassName = Brand::class;
        }

        $quantity = (int)$request->input('quantity', 30);
        if ($quantity > 120 || $quantity < 1) {
            $quantity = 30;
        }

        $search = Helper::fullTextSearchPrepare($request->input('search', ''));
        $searchStrict = Helper::fullTextSearchPrepareStrict($request->input('search', ''));
        $match = "MATCH(body) AGAINST('" . $search . "' IN BOOLEAN MODE)";
        $matchStrict = "MATCH(body) AGAINST('" . $searchStrict . "' IN BOOLEAN MODE)";

        $searchQuery = Search::select('searchable_id')
            ->where('searchable_type', $typeClassName)
            ->where('body', 'like', '%' . $search . '%');
        $queryClone = clone $searchQuery;
        $count = $queryClone->take(1)->count();
        if ($count == 0) {
            $searchQuery = Search::select('searchable_id')
                ->where('searchable_type', $typeClassName)
                ->whereRaw($matchStrict)
                ->orderByRaw($matchStrict . ' DESC');
            // $count = $searchQuery->count();
            // if ($count == 0) {
            //     $searchQuery = Search::select('searchable_id')
            //         ->where('searchable_type', $typeClassName)
            //         ->whereRaw($match)
            //         ->orderByRaw($match . ' DESC');
            // }
        }

        switch ($type) {
            case 'categories':
                $categories = collect();
                $categoryIDs  = $searchQuery->take(1000)->get()->pluck('searchable_id')->toArray();
                if ($categoryIDs) {
                    $categories = Category::active()->withTranslation($locale)->whereIn('id', $categoryIDs)->orderByRaw("FIELD(id," . implode(',', $categoryIDs) . ")")->paginate($quantity)->appends($request->all());
                }
                return CategoryResource::collection($categories);

            case 'brands':
                $brands = collect();
                $brandIDs  = $searchQuery->take(1000)->get()->pluck('searchable_id')->toArray();
                if ($brandIDs) {
                    $brands = Brand::active()->withTranslation($locale)->whereIn('id', $brandIDs)->orderByRaw("FIELD(id," . implode(',', $brandIDs) . ")")->paginate($quantity)->appends($request->all());
                }
                return BrandResource::collection($brands);

            default:
                $products = collect();
                $productIDs  = $searchQuery->take(1000)->get()->pluck('searchable_id')->toArray();
                if ($productIDs) {
                    $products = Product::active()
                        ->whereIn('id', $productIDs)
                        ->withTranslation($locale)
                        ->with(['stickers' => function($q1) use ($locale) {
                            $q1->withTranslation($locale);
                        }])
                        ->with(['brand' => function($q1) use ($locale) {
                            $q1->withTranslation($locale);
                        }])
                        ->orderByRaw("FIELD(id," . implode(',', $productIDs) . ")")
                        ->paginate($quantity)
                        ->appends($request->all());
                }
                return ProductResource::collection($products);
        }
    }
}
