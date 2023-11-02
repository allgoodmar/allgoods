<?php

namespace App\Http\Controllers;

use App\Banner;
use App\Brand;
use App\Category;
use App\Helpers\Breadcrumbs;
use App\Helpers\Helper;
use App\Helpers\LinkItem;
use App\Page;
use App\Product;
use App\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\SchemaOrg\AggregateRating;
use Spatie\SchemaOrg\Schema;

class CategoryController extends Controller
{
    /**
     * show products per page values
     */
    public $quantityPerPage = [30, 60, 120];
    public $sorts = ['views-desc', 'created_at-desc', 'price-asc', 'price-desc', 'rating-desc'];

    /**
     * show products per page values
     */
    public $filters = ['special', 'popular', 'new'];

    public function index()
    {
        $locale = app()->getLocale();

        $breadcrumbs = new Breadcrumbs();
        $page = Helper::translation(Page::findOrFail(17));
        $breadcrumbs->addItem(new LinkItem($page->name, $page->url, LinkItem::STATUS_INACTIVE));

        $categories = Category::active()
            ->whereNull('parent_id')
            ->orderBy('order')
            ->withTranslation($locale)
            ->get();
        if ($categories) {
            $categories = $categories->translate();
        }  

        return view('categories.index', compact('page', 'breadcrumbs', 'categories'));
    }

    public function show(Request $request, Category $category)
    {
        if ($category->parent_id == null) {
            return $this->showParent($request, $category);
        }

        $locale = app()->getLocale();
        $breadcrumbs = new Breadcrumbs();

        // $page = Helper::translation(Page::findOrFail(5));

        // $breadcrumbs->addItem(new LinkItem($page->name, $page->url));

        // quantity per page
        $quantityPerPage = $this->quantityPerPage;
        $quantity = request('quantity', $this->quantityPerPage[0]);
        if (!in_array($quantity, $this->quantityPerPage)) {
            $quantity = $this->quantityPerPage[0];
        }

        // sort - order
        $sorts = $this->sorts;
        $sortCurrent = $request->input('sort', '');
        if (empty($sortCurrent) || !in_array($sortCurrent, $sorts)) {
            $sortCurrent = $sorts[0];
        }
        $sortRaw = explode('-', $sortCurrent);
        $sort = $sortRaw[0];
        $order = $sortRaw[1];

        // product view
        $productView = $request->input('product_view', 'grid');
        if (empty($productView) || !in_array($productView, ['grid', 'list'])) {
            $productView = 'grid';
        }

        // min and max price (per month)
        $prices = [];
        $categoryPrices = [];
        $requestPrices = $request->input('price', []);
        $prices['from'] = $categoryPrices['from'] = isset($requestPrices['from']) ? $requestPrices['from'] : null;
        $prices['to'] = $categoryPrices['to'] = isset($requestPrices['to']) ? $requestPrices['to'] : null;

        // filters
        $filter = $request->input('filter', '');
        if (!empty($filter) && !in_array($filter, $this->filters)) {
            $filter = '';
        }

        // brands
        $brands = $request->input('brand', []);
        if (!empty($brands) && !is_array($brands)) {
            $brands = [];
        }

        // attributes
        $attributes = $request->input('attribute', []);
        if (!empty($attributes) && !is_array($attributes)) {
            $attributes = [];
        }

        $query = $category->products()->active();


        // apply filters
        if ($filter) {
            $query->where('is_' . $filter, constant('\App\Product::' . mb_strtoupper($filter) . '_ACTIVE'));
        }

        // category brands, attributes and attribute values (before attribute and brand filter applied)
        $queryClone = clone $query;
        $categoryBrands = $category->allBrands($queryClone);
        $queryClone = clone $query;
        $categoryAttributes = $category->allAttributes($queryClone);

        // get min max prices
        $queryClone = clone $query;
        $categoryPrices['min'] = $queryClone->select('price')->min('price');
        $categoryPrices['max'] = $queryClone->select('price')->max('price');

        // apply brands
        if ($brands) {
            $query->whereIn('products.brand_id', $brands);
        }

        // apply attributes
        if ($attributes) {
            foreach($attributes as $key => $values) {
                $attributeValueIds = [];
                $values = array_map('intval', $values);
                $attributeValueIds = array_merge($attributeValueIds, $values);
                if ($attributeValueIds) {
                    $query->whereIn('products.id', function($q1) use ($attributeValueIds) {
                        $q1->select('products.id')->from('products')->whereIn('products.id', function($q2) use ($attributeValueIds) {
                            $q2->select('product_id')->from('attribute_value_product')->whereIn('attribute_value_id', $attributeValueIds);
                        });
                    });
                }
            }
        }

        // apply prices
        if (isset($prices['from']) && isset($prices['to'])) {
            if ($prices['from'] > $prices['to']) {
                $tmp = $prices['from'];
                $prices['from'] = $prices['to'];
                $prices['to'] = $tmp;
            }
            $query->where('products.price', '>=', $prices['from'])
                  ->where('products.price', '<=', $prices['to']);
        }

        // not hidden products (product groups)
        $query->where('products.is_hidden', 0);

        // sorting
        $query->orderByRaw('products.in_stock = 0');

        $query->orderBy('products.order', 'ASC');
        $query
            ->orderBy('products.' . $sort, $order);

        $productAllQuantity = $query->count();

        $query
            ->with('categories')
            ->withTranslation($locale)
            ->with('activeReviews');

        // microdata
        $queryClone = clone $query;
        // $itemIds = $queryClone->get()->pluck('id');
        $microdata = '';
        // if ($itemIds) {
        //     $reviewsInfo = DB::table('reviews')->selectRaw('count(*) count, avg(rating) avg, sum(rating) sum')->where('status', 1)->where('reviewable_type', Product::class)->whereIn('reviewable_id', $itemIds)->first();
        //     $reviewsCount = $reviewsInfo->count;
        //     $reviewsAvg = round($reviewsInfo->avg, 1);

        //     // product
        //     $appName = config('app.name');
        //     $microdata = Schema::product();
        //     $microdata->name($category->name);
        //     $microdata->brand($appName);
        //     $microdata->description($category->description ?: $category->name);
        //     $microdata->image($category->img);
        //     $microdata->sku($appName . ' Category ' . $category->id);
        //     $microdata->mpn($appName . ' Category ' . $category->id);

        //     // offers
        //     $lowPriceProduct = Product::active()->whereIn('id', $itemIds)->select('price')->orderBy('price')->first();
        //     $highPriceProduct = Product::active()->whereIn('id', $itemIds)->select('price')->orderBy('price', 'desc')->first();
        //     if ($lowPriceProduct && $highPriceProduct) {
        //         $microdataOffer = Schema::aggregateOffer();
        //         $microdataOffer->lowPrice(round($lowPriceProduct->price));
        //         $microdataOffer->highPrice(round($highPriceProduct->price));
        //         $microdataOffer->priceCurrency('UZS');
        //         $microdataOffer->offerCount(count($itemIds));
        //         $microdata->offers($microdataOffer);
        //     }

        //     // rating
        //     $aggregateRating = new AggregateRating();
        //     if ($reviewsCount > 0) {
        //         $aggregateRating->worstRating(1)->bestRating(5)->ratingCount($reviewsCount)->ratingValue($reviewsAvg);
        //     } else {
        //         $aggregateRating->worstRating(1)->bestRating(5)->ratingCount(1)->ratingValue(5);
        //     }
        //     $microdata->aggregateRating($aggregateRating);

        //     // reviews
        //     $categoryReviews = Review::where('status', 1)->where('reviewable_type', Product::class)->whereIn('reviewable_id', $itemIds)->get();
        //     $microdataReviews = [];
        //     foreach($categoryReviews as $review) {
        //         $microdataReview = Schema::review();
        //         $microdataReview->name($category->name);
        //         $microdataReview->reviewBody($review->body);
        //         $microdataReview->author($review->name);
        //         $microdataReview->datePublished($review->created_at->format('Y-m-d'));
        //         $microdataReview->reviewRating(Schema::rating()->bestRating(5)->worstRating(1)->ratingValue($review->rating));
        //         $microdataReviews[] = $microdataReview;
        //     }
        //     if (count($microdataReviews)) {
        //         $microdata->review($microdataReviews);
        //     }

        //     // get script
        //     $microdata = $microdata->toScript();
        // } else {
        //     $microdata = '';
        // }

        // get query products paginate
        $products = $query->paginate($quantity);

        $appends = ['quantity' => $quantity, 'sort' => $sortCurrent, 'attribute' => $attributes, 'brand' => $brands, 'product_view' => $productView];
        if (isset($prices['from']) && isset($prices['to'])) {
            $appends['price'] = $prices;
        }
        $links = $products->appends($appends)->links('partials.pagination');
        $total = $products->total();

        $products = $products->translate();

        if($category->parent) {
            $parent = Helper::translation($category->parent);
            $breadcrumbs->addItem(new LinkItem($parent->name, $parent->url));
        }

        // categories
        // $categories = Category::active()->parents()->with('children.children.children')->get();
        // $siblingCategories = Category::active()->where('parent_id', $category->parent_id)->get();
        $subcategories = Category::active()
            ->where('parent_id', $category->id)
            ->orderBy('order')
            ->withTranslation($locale)
            ->get();
        if ($subcategories) {
            $subcategories = $subcategories->translate();
        }

        // current and its parent category ids
        $activeCategoryIds = Helper::activeCategories($category);

        $category = Helper::translation($category);

        $new_products = Product::active()->latest()->take(4)->get();

        $breadcrumbs->addItem(new LinkItem($category->name, $category->url, LinkItem::STATUS_INACTIVE));

        // GET PARENT CATEGORY ID

        $parent_cat_one = Category::where('id', $category->parent_id)->first();
        $parent_category_go = Category::where('id', $parent_cat_one->parent_id)->first();

        $parentcatgo = $parent_category_go->name ?? '';

        // END

        return view('categories.show', compact('parentcatgo', 'breadcrumbs', 'products', 'productAllQuantity', 'category', 'new_products', 'activeCategoryIds', 'subcategories', 'links', 'total', 'brands', 'attributes', 'quantity', 'quantityPerPage', 'sorts', 'sortCurrent', 'productView', 'categoryBrands', 'categoryAttributes', 'categoryPrices', 'prices', 'microdata'));
    }

    private function showParent(Request $request, Category $category)
    {
        $locale = app()->getLocale();
        $breadcrumbs = new Breadcrumbs();

        $page = Helper::translation(Page::findOrFail(17));
        $breadcrumbs->addItem(new LinkItem($page->name, $page->url));

        $subcategories = Category::active()
            ->where('parent_id', $category->id)
            ->orderBy('order')
            ->withTranslation($locale)
            ->get();
        if ($subcategories) {
            $subcategories = $subcategories->translate();
        }

        $slides = Banner::where('category_id', $category->id)->where('type', Banner::TYPE_SLIDE)->active()->latest()->get();
        if (!$slides->isEmpty()) {
            $slides = $slides->translate();
        }

        $category = Helper::translation($category);
        $breadcrumbs->addItem(new LinkItem($category->name, $category->url, LinkItem::STATUS_INACTIVE));

        return view('categories.show_parent', compact('breadcrumbs', 'category', 'subcategories', 'slides'));
    }

    public function individual(Request $request, Brand $brand_id, $category_id)
    {
        $brand = $brand_id;
        $breadcrumbs = new Breadcrumbs();

        $locale = app()->getLocale();

        $page = Helper::translation(Page::findOrFail(16));
        $breadcrumbs->addItem(new LinkItem($page->name, $page->url));

        $currentRegion = Helper::getCurrentRegion();
        $warehouseIDs = $currentRegion->warehouses->pluck('id')->toArray();

        // quantity per page
        $quantityPerPage = $this->quantityPerPage;
        $quantity = $request->input('quantity', $this->quantityPerPage[0]);
        if (!in_array($quantity, $this->quantityPerPage)) {
            $quantity = $this->quantityPerPage[0];
        }

        // sort - order
        $sorts = $this->sorts;
        $sortCurrent = $request->input('sort', '');
        if (empty($sortCurrent) || !in_array($sortCurrent, $sorts)) {
            $sortCurrent = $sorts[0];
        }
        $sortRaw = explode('-', $sortCurrent);
        $sort = $sortRaw[0];
        $order = $sortRaw[1];

        // $category_id = substr($category_id, strpos($category_id, "-") + 1);
        // dd($category_id);

        $query = $brand->products()
            ->select("products.*", "category_product.id as cat_id")
            ->where('category_product.category_id', '=', $category_id)
            ->join('category_product', 'category_product.product_id', '=', 'products.id')
            ->orderBy('products.' . $sort, $order)
            ->active()
            ->with(['categories' => function($query) use ($locale) {
                $query->withTranslation($locale);
            }])
            ->withTranslation($locale)
            ->orderBy('products.created_at');

        $productAllQuantity = $query->count();

        // get query products paginate
        $products = $query->paginate($quantity);
        $links = $products->links('partials.pagination');
        $links = $products->appends(['sort' => $sortCurrent])->links();

        if (!$products->isEmpty()) {
            $products = $products->translate();
        }

        $brandCategories = Category::active()
            ->whereNotNull('parent_id')
            ->whereHas('products', function($q1) use ($brand) {
                $q1->active()->where('brand_id', $brand->id);
            })
            ->whereHas('parent', function($q2) {
                $q2->whereNull('parent_id');
            })
            ->with('parent', function($q3) use ($locale) {
                $q3->withTranslation($locale);
            })
            ->withTranslation($locale)
            ->get();

        $brand = Helper::translation($brand);

        $breadcrumbs->addItem(new LinkItem($brand->name, $brand->url, LinkItem::STATUS_INACTIVE));

        return view('brands.show', compact('brandCategories', 'page', 'breadcrumbs', 'products', 'productAllQuantity', 'brand', 'links', 'quantity', 'quantityPerPage', 'sorts', 'sortCurrent'));
    }

}
