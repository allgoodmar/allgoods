<?php

namespace App\Http\Controllers\Api\V2;

use App\Attribute;
use App\Category;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttributeResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StickerResource;
use App\Product;
use App\Search;
use App\Sticker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $quantity = (int)$request->input('quantity', 30);
        if ($quantity > 120 || $quantity < 1) {
            $quantity = 30;
        }
        $categoryId = $request->input('category_id', null);
        // $brandId = $request->input('brand_id', null);
        $brands = $request->input('brands', []);
        if (!empty($brands) && !is_array($brands)) {
            $brands = [];
        }
        $attributes = $request->input('attributes', []);
        if (!empty($attributes) && !is_array($attributes)) {
            $attributes = [];
        }

        $search = Helper::fullTextSearchPrepare($request->input('search', ''));
        $searchStrict = Helper::fullTextSearchPrepareStrict($request->input('search', ''));
        $match = "MATCH(body) AGAINST('" . $search . "' IN BOOLEAN MODE)";
        $matchStrict = "MATCH(body) AGAINST('" . $searchStrict . "' IN BOOLEAN MODE)";

        $priceFrom = (float)$request->input('price_from', -1);
        $priceTo = (float)$request->input('price_to', -1);
        $isNew = (int)$request->input('is_new', -1);
        $isBestseller = (int)$request->input('is_bestseller', -1);
        $isPromotion = (int)$request->input('is_promotion', -1);

        $orderBy = $request->input('order_by', 'created_at');
        if (!in_array($orderBy, ['created_at', 'price', 'views', 'rating'])) {
            $orderBy = 'created_at';
        }
        $orderBy = 'products.' . $orderBy;
        $orderDirection = $request->input('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }


        // create auery
        if ($categoryId) {
            $category = Category::findOrFail($categoryId);
            $query = $category->products();
        } else {
            $query = Product::query();
        }

        // main requirements
        $query->active()
            ->withTranslation($locale)
            ->with(['stickers' => function($q1) use ($locale) {
                $q1->withTranslation($locale);
            }])
            ->with(['brand' => function($q1) use ($locale) {
                $q1->withTranslation($locale);
            }]);

        // brand
        // if ($brandId) {
        //     $query->where('products.brand_id', $brandId);
        // }

        // brands
        if ($brands) {
            $query->whereIn('products.brand_id', $brands);
        }

        // price
        if ($priceFrom >= 0) {
            $query->where('products.price', '>=', $priceFrom);
        }
        if ($priceTo >= 0) {
            $query->where('products.price', '<=', $priceTo);
        }

        // flags
        if ($isNew >= 0) {
            $query->where('products.is_new', 1);
        }
        if ($isBestseller >= 0) {
            $query->where('products.is_bestseller', 1);
        }
        if ($isPromotion >= 0) {
            $query->where('products.is_promotion', 1);
        }

        // not hidden products (product groups)
        $query->where('products.is_hidden', 0);

        // attributes, attribute values
        if ($attributes) {
            foreach($attributes as $values) {
                if (!is_array($values)) {
                    continue;
                }
                $attributeValueIds = [];
                $values = array_map('intval', $values);
                $attributeValueIds = array_merge($attributeValueIds, $values);
                if ($attributeValueIds) {
                    $query->whereIn('products.id', function($q1) use ($attributeValueIds) {
                        $q1->select('products.id')->from('products')->whereIn('products.id', function($q2) use ($attributeValueIds) {
                            $q2->select('attribute_value_product.product_id')->from('attribute_value_product')->whereIn('attribute_value_product.attribute_value_id', $attributeValueIds);
                        });
                    });
                }
            }
        }

        // search
        if ($searchStrict && Str::length($searchStrict) >= 3) {

            $searchQuery = Search::select('searchable_id')
                ->where('body', 'like', '%' . $search . '%')
                ->where('searchable_type', Product::class);
            $queryClone = clone $searchQuery;
            $count = $queryClone->take(1)->count();
            if ($count == 0) {
                $searchQuery = Search::select('searchable_id')
                    ->whereRaw($matchStrict)
                    ->where('searchable_type', Product::class)
                    ->orderByRaw($matchStrict . ' DESC');
                $queryClone = clone $searchQuery;
                $count = $queryClone->take(1)->count();
                if ($count == 0) {
                    $searchQuery = Search::select('searchable_id')
                        ->whereRaw($match)
                        ->where('searchable_type', Product::class)
                        ->orderByRaw($match . ' DESC');
                }
            }

            $productIDs = $searchQuery->take(1000)->pluck('searchable_id')->toArray();
            if (!$productIDs) {
                $productIDs = [-1];
            }
            $query->whereIn('products.id', $productIDs)->orderByRaw("FIELD(products.id," . implode(',', $productIDs) . ")");
        }

        // sort order
        // $query->orderByRaw('products.in_stock = 0');
        $query->orderBy($orderBy, $orderDirection);

        // get products
        $products = $query->paginate($quantity)->appends($request->all());

        return ProductResource::collection($products);
    }

    public function show(Request $request, Product $product)
    {
        $locale = app()->getLocale();
        $product->increment('views');
        $product->load('translations');
        $product->load([
            'categories' => function($query) use ($locale) {
                $query->active()->withTranslation($locale);
            },
            'brand' => function($query) use ($locale) {
                $query->withTranslation($locale);
            },
            'stickers' => function($query) use ($locale) {
                $query->withTranslation($locale);
            },
        ]);
        return new ProductResource($product);
    }

    public function attributes(Request $request, Product $product)
    {
        $locale = app()->getLocale();

        $attributeValueIds = DB::table('attribute_value_product')->where('attribute_value_product.product_id', $product->id)->pluck('attribute_value_product.attribute_value_id')->unique();

        $attributeIds = DB::table('attribute_values')
            ->leftJoin('attributes', 'attribute_values.attribute_id', '=', 'attributes.id')
            ->whereIn('attribute_values.id', $attributeValueIds->toArray())
            ->pluck('attributes.id')
            ->unique();

        $query = Attribute::whereIn('attributes.id', $attributeIds->toArray())
            ->withTranslation($locale)
            ->with(['attributeValues' => function ($q1) use ($locale, $attributeValueIds) {
                $q1->whereIn('attribute_values.id', $attributeValueIds->toArray())
                    ->withTranslation($locale);
            }]);
        $attributes = $query->get();
        return AttributeResource::collection($attributes);
    }

    public function stickers(Request $request, Product $product)
    {
        $locale = app()->getLocale();
        $stickers = $product->stickers()->active()->orderBy('stickers.order')->withTranslation($locale)->get();
        return StickerResource::collection($stickers);
    }

    public function categories(Request $request, Product $product)
    {
        $locale = app()->getLocale();
        $categories = $product->categories()->active()->withTranslation($locale)->get();
        return CategoryResource::collection($categories);
    }

    public function similar(Request $request, Product $product)
    {
        $locale = app()->getLocale();
        $products = collect();
        $category = $product->categories()->orderBy('parent_id', 'desc')->first();
        if ($category) {
            $products = $category->products()
                ->active()
                ->withTranslation($locale)
                ->with(['stickers' => function($q1) use ($locale) {
                    $q1->withTranslation($locale);
                }])
                ->with(['brand' => function($q1) use ($locale) {
                    $q1->withTranslation($locale);
                }])
                ->latest()
                ->take(10)
                ->get();
        }
        return ProductResource::collection($products);
    }

    public function recommended(Request $request, Product $product)
    {
        $locale = app()->getLocale();
        $products = Product::active()
            ->where('brand_id', $product->brand_id)
            ->withTranslation($locale)
            ->with(['stickers' => function($q1) use ($locale) {
                $q1->withTranslation($locale);
            }])
            ->with(['brand' => function($q1) use ($locale) {
                $q1->withTranslation($locale);
            }])
            ->latest()
            ->take(10)
            ->get();
        return ProductResource::collection($products);
    }
}
