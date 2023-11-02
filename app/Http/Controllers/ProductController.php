<?php

namespace App\Http\Controllers;

use App\Attribute;
use App\AttributeValue;
use App\Helpers\Breadcrumbs;
use App\Helpers\Helper;
use App\Helpers\LinkItem;
use App\Page;
use App\Product;
use App\Brand;
use App\Category;
use App\Services\IntendService;
use Illuminate\Http\Request;
use App\Helpers\Rating;
use App\SellerCompany;
use App\Services\TrendyolService;
use App\Shop;
use App\SessionRecentProduct;
use App\Youtubelink;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Spatie\SchemaOrg\AggregateRating;
use Spatie\SchemaOrg\Schema;

class ProductController extends Controller
{

    /**
     * show products per page values
     */
    public $quantityPerPage = [12, 60, 120];
    public $sorts = ['views-desc', 'created_at-desc', 'price-asc', 'price-desc', 'rating-desc'];
    public $intendService;

    public function __construct(IntendService $intendService)
    {
        $this->intendService = $intendService;
        $this->authorizeResource(Product::class, 'product');
    }

    public function view(Request $request, Product $product, $slug)
    {

        if (!empty(session()->getId())) {
            $current_session = session()->getId();
            $SessionRecentProduct = SessionRecentProduct::where([['product_id', $product->id],['sessionId', $current_session]])->first();
            if (empty($SessionRecentProduct)) {
                SessionRecentProduct::create([
                    'sessionId' => $current_session,
                    'product_id' => $product->id
                ]);
            }
        }

        Helper::checkModelActive($product);
        if ($product->getTranslatedAttribute('slug') != $slug) {
            abort(404);
        }
        // $page = Page::findOrFail(4);
        $breadcrumbs = new Breadcrumbs();
        //$breadcrumbs->addItem(new LinkItem($page->name, $page->url));

        // update stock
        // if ($product->isTrendyolProduct()) {
        //     Helper::trendyolUpdateStock([$product->barcode]);
        //     $product->refresh();
        // }

        // $this->getLocationOfUser();
        $locale = app()->getLocale();

        // $currentRegionID = Helper::getCurrentRegionID();
        $currentRegion = Helper::getCurrentRegion();
        $warehouseIDs = $currentRegion->warehouses->pluck('id')->toArray();

        $product->increment('views');

        $latestViewedProductIDs = Cache::get('latest_viewed_products_ids');
        if ($latestViewedProductIDs) {
            $latestViewedProductIDs = explode(',', $latestViewedProductIDs);
            array_unshift($latestViewedProductIDs, $product->id);
            if (count($latestViewedProductIDs) >= 100) {
                $latestViewedProductIDs = array_slice($latestViewedProductIDs, 0, 100);
            }
            $latestViewedProductIDs = implode(',', $latestViewedProductIDs);
        } else {
            $latestViewedProductIDs = $product->id;
        }
        Cache::put('latest_viewed_products_ids', $latestViewedProductIDs);

        $productGroup = $product->productGroup;
        if ($productGroup) {
            $productGroup->load(['products' => function($q){
                $q->with(['attributes', 'attributeValues.attribute']);
            }]);
        }

        $brand = $product->brand;
        if (!empty($brand)) {
            $brand = Helper::translation($brand);
        }
        // $shop = $product->shop;
        // if($shop) {
        //     $shop = $shop->translate();
        // }

        $category = $product->categories->first();
        if (!empty($category)) {
            if (!empty($category->parent)) {
                $parent = Helper::translation($category->parent);
                $breadcrumbs->addItem(new LinkItem($parent->name, $parent->url));
            }else{
                $category = Helper::translation($category);
                $breadcrumbs->addItem(new LinkItem($category->name, $category->url));
            }
        }

        // Breadcrumb parent of model
        $parent_model_name = '';
        /* category ex: SAMSUNG, XIAOMI
        $par = $product->categories->last();
        if (!empty($par->parent)) {
            $parent_model_name = Helper::translation($par->parent);
            $breadcrumbs->addItem(new LinkItem($parent_model_name->name, $parent_model_name->url));
        }*/


        // show BRAND in Breadcrumb
        if (!empty($product->brand_id)) {
            $brand_bread = Brand::where('id', $product->brand_id)->first();
            $brand_bread_url = "https://allgood.uz/brand/".$brand_bread->id."-".$brand_bread->slug;
            $breadcrumbs->addItem(new LinkItem($brand_bread->name, $brand_bread_url));
        }

        // show MODEL in Breadcrumb and show in product_page
        $model_name = '';
        $par = $product->categories->last();
        if (!empty($par)) {
            $model_name = Helper::translation($par);
            $breadcrumbs->addItem(new LinkItem($model_name->name, $model_name->url));
        }

        // similar products
        $similarProductsQuery = $product->similar()
            ->active()
            ->with(['categories' => function ($query) use ($locale) {
                $query->withTranslation($locale);
            }])
            ->withTranslation($locale)
            ->orderBy('products.created_at');
        $similarProducts = $similarProductsQuery->take(12)->get();
        if (!$similarProducts->isEmpty()) {
            $similarProducts = $similarProducts->translate();
        }

        $prev = $product->similar()->active()->where('products.id', '<', $product->id)->orderBy('products.id', 'desc')->first();
        if ($prev) {
            $prev = $prev->translate();
        }
        $next = $product->similar()->active()->where('products.id', '>', $product->id)->orderBy('products.id', 'asc')->first();
        if ($next) {
            $next = $next->translate();
        }

        // reviews
        $reviewsQuery = $product->reviews()->active();
        $reviewsQuantity = $reviewsQuery->count();
        $reviews = $reviewsQuery->latest()->take(20)->get();

        // attributes
        $attributeValueIds = $product->attributeValuesIds();
        $attributes = $product->attributesOrdered()->withTranslation($locale)->with(['attributeValues' => function ($q1) use ($attributeValueIds, $locale) {
            $q1->whereIn('id', $attributeValueIds)->withTranslation($locale);
        }])->get()->translate();

        $product = Helper::translation($product);
        // $breadcrumbs->addItem(new LinkItem($product->name, $product->url, LinkItem::STATUS_INACTIVE));

        // SEO templates
        // $product = Helper::seoTemplate($product, 'product', ['name' => $product->name]);

        $microdata = Schema::product();
        $microdata->name($product->name);
        $microdata->sku($product->sku);
        if (!empty($brand)) {
            $microdata->brand($brand->name);
        }
        $microdata->image($product->img);
        $microdata->description(Str::limit(strip_tags($product->body), 200, '...'));
        if ($product->rating > 0) {
            $aggregateRating = new AggregateRating();
            $aggregateRating->worstRating(1)->bestRating(5)->ratingCount($product->active_reviews_count)->ratingValue($product->rating);
            $microdata->aggregateRating($aggregateRating);
        }
        $offer = Schema::offer();
        $offer->url($product->url);
        $offer->price($product->current_price);
        $offer->priceCurrency('UZS');
        $offer->priceValidUntil(now()->addMonths(3)->format('Y-m-d'));
        if ($product->getModel()->getStock() > 0) {
            $offer->availability('https://schema.org/InStock');
        } else {
            $offer->availability('https://schema.org/OutOfStock');
        }
        $microdata->offers($offer);
        $microdata = $microdata->toScript();

        $deliveryText = Helper::translation(Helper::staticText('delivery_text'))->description;

        $faqPages = Page::active()->withTranslation($locale)->whereIn('id', [8, 9, 10, 11])->get()->translate();
        $partnersPrices = Helper::partnersPrices($product->current_price);

        // $intend_price = $this->intendService->calculateIntend($product);

        $seoTitle = $product->seo_title;
        $metaDescription = $product->meta_description;
        $metaKeywords = $product->meta_keywords;

        if (!empty($seoTitle)){
            $seoTitle = $product->seo_title;
        }else{

            $first_word = strtok($product->name, " ");
            $first_word = Str::lower($first_word);
            $other_words = strstr($product->name," ");
            if (app()->getLocale() == 'ru') {
                $seoTitle = "Купить " . $first_word . $other_words . " в Ташкенте, цены";
            }else{
                $seoTitle = "Toshkentda " . $first_word . $other_words . " sotib oling, narxlar";
            }
        }

        if (!empty($metaDescription)){
            $metaDescription = $product->meta_description;
        }else{
            if (app()->getLocale() == 'ru') {
                $metaDescription = "В интернет-магазине Allgood.uz.можно купить " . Str::lower($product->name) . " в Ташкенте. Продукция " . Str::lower($product->name) . " по доступным ценам в каталоге. Заказать с доставкой по Узбекистану";
            }else{
                $metaDescription = "Allgood.uz internet-do'konida siz " . Str::lower($product->name) . " mahsulotlarini xarid qilishingiz mumkin. Ushbu " . Str::lower($product->name) . " ni mahsulotlari katalogda arzon narxlarda. Buyurtma bering va O'zbekiston bo'ylab yetkazib beramiz";
            }
        }

        /*
        if (!empty($product->brand_id)) {
            $brand_bread = Brand::where('id', $product->brand_id)->first();
            $brand_bread_url = "https://allgood.uz/brand/".$brand_bread->id."-".$brand_bread->slug;
            $breadcrumbs->addItem(new LinkItem($brand_bread->name, $brand_bread_url));
        }
        */

        $youtubelinks = Youtubelink::where('product_id', $product->id)->orderBy('order', 'ASC')->get();
        $youtubelinksOne = Youtubelink::where('product_id', $product->id)->orderBy('order', 'ASC')->limit(1)->first();

        $seller = '';
        $seller = SellerCompany::where('id', $product->seller_id)->first();

        $data = compact('breadcrumbs', 'product', 'youtubelinks', 'youtubelinksOne', 'productGroup', 'parent_model_name', 'model_name', 'brand', 'category', 'attributes', 'reviewsQuantity', 'reviews', 'similarProducts', 'microdata', 'prev', 'next', 'deliveryText', 'faqPages', 'partnersPrices', 'seoTitle', 'metaDescription', 'metaKeywords', 'seller');

        if ($request->input('json', '')) {
            return response()->json([
                'product_id' => $product->id,
                'product_url' => $product->url,
                'main' => view('product.partials.product_page_content', $data)->render(),
                'seo_itle' => $seoTitle,
                'meta_description' => $metaDescription,
                'meta_keywords' => $metaKeywords,
            ]);
        }

        return view('product.view', $data);
    }

    /* resources */
    public function show(Product $product)
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.profile'), route('profile.show')));
        $breadcrumbs->addItem(new LinkItem(__('main.products'), route('profile.products')));
        $breadcrumbs->addItem(new LinkItem(__('main.product'), route('products.show', $product->id), LinkItem::STATUS_INACTIVE));
        return view('product.show', compact('breadcrumbs', 'product'));
    }

    public function create()
    {
        $product = new Product();
        $categories = Category::active()->orderBy('name')->get();
        $category_id = request()->input('category_id', '');
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.profile'), route('profile.show')));
        $breadcrumbs->addItem(new LinkItem(__('main.products'), route('profile.products')));
        $breadcrumbs->addItem(new LinkItem(__('main.add'), route('products.create'), LinkItem::STATUS_INACTIVE));
        return view('product.create', compact('breadcrumbs', 'product', 'categories', 'category_id'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $user = auth()->user();
        // $shop = $user->shops()->first();
        // if (!$shop) {
        //     $shop = Shop::craete([
        //         'user_id' => $user->id,
        //         'name' => 'Shop',
        //     ]);
        // }

        // set additional data
        $data['in_stock'] = $request->has('in_stock') ? 1 : 0;
        $data['slug'] = Str::slug($data['name']);
        $data['status'] = Product::STATUS_PENDING;
        $data['unique_code'] = uniqid();
        $data['user_id'] = $user->id;
        // $data['shop_id'] = $shop->id;

        $product = Product::create($data);

        Helper::storeImage($product, 'image', 'products', Product::$imgSizes);

        Session::flash('message', __('main.data_saved') . '. ' . __('main.pending_moderator_review'));
        return redirect()->route('profile.products');
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->orderBy('name')->get();
        $category_id = request()->input('category_id', '');
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.profile'), route('profile.show')));
        $breadcrumbs->addItem(new LinkItem(__('main.products'), route('profile.products')));
        $breadcrumbs->addItem(new LinkItem(__('main.edit'), route('products.edit', $product->id), LinkItem::STATUS_INACTIVE));
        return view('product.edit', compact('breadcrumbs', 'product', 'categories', 'category_id'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validatedData($request);

        // set additional data
        $data['in_stock'] = $request->has('in_stock') ? 1 : 0;
        $data['status'] = Product::STATUS_PENDING;

        $product->update($data);

        Helper::storeImage($product, 'image', 'products', Product::$imgSizes);

        Session::flash('message', __('main.data_saved') . '. ' . __('main.pending_moderator_review'));
        return redirect()->route('profile.products');
    }

    public function destroy(Request $request, Product $product)
    {
        // TODO: delete image
        // Helper::deleteImage($product, 'image', Product::$imgSizes);

        $product->delete();


        Session::flash('message', __('main.data_deleted'));
        return redirect()->route('profile.products');
    }

    protected function validatedData(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:190',
            'price' => 'required|numeric|max:1000000000',
            'sale_price' => '',
            'description' => 'max:1000',
            'image' => 'sometimes|image|max:1000',
            'body' => '',
        ]);
        $data['sale_price'] = (float)$data['sale_price'];

        return $data;
    }


    public function attributesEdit(Product $product)
    {
        $this->authorize('update', $product);

        $attributes = Attribute::all();
        $attributeValueIds = $product->attributeValues()->pluck('attribute_value_id')->toArray();

        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.profile'), route('profile.show')));
        $breadcrumbs->addItem(new LinkItem(__('main.products'), route('profile.products')));
        $breadcrumbs->addItem(new LinkItem(__('main.edit'), route('products.edit', $product->id)));
        $breadcrumbs->addItem(new LinkItem(__('main.attributes'), route('products.attributes.edit', $product->id), LinkItem::STATUS_INACTIVE));

        return view('product.edit-attributes', compact('breadcrumbs', 'product', 'attributes', 'attributeValueIds'));
    }

    public function attributesUpdate(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $attributes = $request->input('attributes', []);

        // product attributes
        $productAttributes = [];
        foreach ($attributes as $key => $attribute) {
            $productAttributes[$key] = [
                'used_for_variations' => (isset($attribute['used_for_variations']) && $attribute['used_for_variations']) ? 1 : 0,
            ];
        }
        $product->attributes()->sync($productAttributes);

        // product attribute values
        $productAttributeValues = [];
        foreach ($attributes as $attribute) {
            $productAttributeValues = array_merge($productAttributeValues, $attribute['values']);
        }
        $product->attributeValues()->sync($productAttributeValues);

        return redirect()->back()->with([
            'message' => __('main.attributes_saved'),
            'alert-type' => 'success',
        ]);
    }

    public function featured(Request $request)
    {
        $breadcrumbs = new Breadcrumbs();

        $page = Helper::translation(Page::where('slug', 'featured')->firstOrFail());
        $breadcrumbs->addItem(new LinkItem($page->name, $page->url));

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

        $query = Product::featured()->active()->orderBy('products.' . $sort, $order);

        $productAllQuantity = $query->count();

        // get query products paginate
        $products = $query->withTranslation(app()->getLocale())->paginate($quantity);
        $links = $products->links();

        $products = $products->translate();

        return view('featured', compact('page', 'breadcrumbs', 'products', 'productAllQuantity', 'links', 'quantity', 'quantityPerPage', 'sorts', 'sortCurrent'));
    }

    public function sale(Request $request)
    {
        $breadcrumbs = new Breadcrumbs();

        $categoryID = $request->input('category', null);
        $category = null;
        if ($categoryID) {
            $category = Category::findOrFail($categoryID);
        }

        $page = Helper::translation(Page::findOrFail(12));
        $breadcrumbs->addItem(new LinkItem($page->name, $page->url));

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

        if ($category) {
            $query = $category->products();
            $category = $category->translate();
        } else {
            $query = Product::query();
        }

        $query->where('sale_price', '>', 0)->whereColumn('price', '>', 'sale_price')->active()->orderBy('products.' . $sort, $order);

        // Aside categories
        $locale = app()->getLocale();
        $brandCategories = Category::active()
            ->whereNotNull('parent_id')
            ->whereHas('products', function($q1) {
                $q1->active()->where('sale_price', '>', 0)->whereColumn('price', '>', 'sale_price');
            })
            ->whereHas('parent', function($q2) {
                $q2->whereNull('parent_id');
            })
            ->with('parent', function($q3) use ($locale) {
                $q3->withTranslation($locale);
            })
            ->withTranslation($locale)
            ->get();
        // Aside Categories ends

        $productAllQuantity = $query->count();

        // get query products paginate
        $products = $query->withTranslation(app()->getLocale())->paginate($quantity);
        $links = $products->appends(['sort' => $sortCurrent])->links();

        $products = $products->translate();

        return view('sale', compact('brandCategories', 'page', 'breadcrumbs', 'category', 'products', 'productAllQuantity', 'links', 'quantity', 'quantityPerPage', 'sorts', 'sortCurrent'));
    }

    public function individualForSale(Request $request, $category_id)
    {
        $breadcrumbs = new Breadcrumbs();

        $categoryID = $request->input('category', null);
        $category = null;
        if ($categoryID) {
            $category = Category::findOrFail($categoryID);
        }

        $page = Helper::translation(Page::findOrFail(12));
        $breadcrumbs->addItem(new LinkItem($page->name, $page->url));

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
            $sortCurrent = $sorts[2];
        }
        $sortRaw = explode('-', $sortCurrent);
        $sort = $sortRaw[0];
        $order = $sortRaw[1];

        if ($category) {
            $query = $category->products();
            $category = $category->translate();
        } else {
            $query = Product::query();
        }

        $locale = app()->getLocale();

        $query->where('sale_price', '>', 0)->whereColumn('price', '>', 'sale_price');

        $query->select("products.*", "category_product.id as cat_id");
        $query->where('category_product.category_id', '=', $category_id)
            ->join('category_product', 'category_product.product_id', '=', 'products.id')
            ->active()
            ->with(['categories' => function($query) use ($locale) {
                $query->withTranslation($locale);
            }])
            ->withTranslation($locale)
            ->orderBy('products.' . $sort, $order);
        // Aside categories
        $brandCategories = Category::active()
            ->whereNotNull('parent_id')
            ->whereHas('products', function($q1) {
                $q1->active()->where('sale_price', '>', 0)->whereColumn('price', '>', 'sale_price');
            })
            ->whereHas('parent', function($q2) {
                $q2->whereNull('parent_id');
            })
            ->with('parent', function($q3) use ($locale) {
                $q3->withTranslation($locale);
            })
            ->withTranslation($locale)
            ->get();
        // Aside Categories ends

        $productAllQuantity = $query->count();

        // get query products paginate
        $products = $query->withTranslation(app()->getLocale())->paginate($quantity);
        $links = $products->links();

        $products = $products->translate();

        return view('sale', compact('brandCategories', 'page', 'breadcrumbs', 'category', 'products', 'productAllQuantity', 'links', 'quantity', 'quantityPerPage', 'sorts', 'sortCurrent'));
    }

    public function newProducts(Request $request)
    {
        $breadcrumbs = new Breadcrumbs();

        $categoryID = $request->input('category', null);
        $category = null;
        if ($categoryID) {
            $category = Category::findOrFail($categoryID);
        }

        $page = Helper::translation(Page::findOrFail(21));
        $breadcrumbs->addItem(new LinkItem($page->name, $page->url));

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

        if ($category) {
            $query = $category->products();
            $category = $category->translate();
        } else {
            $query = Product::query();
        }

        $query->active()->orderBy('products.' . $sort, $order);

        $productAllQuantity = $query->count();

        // get query products paginate
        $products = $query->withTranslation(app()->getLocale())->paginate($quantity);
        $links = $products->links('partials.pagination');

        $products = $products->translate();

        return view('new_products', compact('page', 'breadcrumbs', 'category', 'products', 'productAllQuantity', 'links', 'quantity', 'quantityPerPage', 'sorts', 'sortCurrent'));
    }

    public function bestsellers(Request $request)
    {
        $breadcrumbs = new Breadcrumbs();

        $categoryID = $request->input('category', null);
        $category = null;
        if ($categoryID) {
            $category = Category::findOrFail($categoryID);
        }

        $page = Helper::translation(Page::findOrFail(14));
        $breadcrumbs->addItem(new LinkItem($page->name, $page->url));

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
            $sortCurrent = $sorts[2];
        }
        $sortRaw = explode('-', $sortCurrent);
        $sort = $sortRaw[0];
        $order = $sortRaw[1];

        if ($category) {
            $query = $category->products();
            $category = $category->translate();
        } else {
            $query = Product::query();
        }

        $query->bestseller()->active()->orderBy('products.' . $sort, $order);

        $productAllQuantity = $query->count();

        // get query products paginate
        $products = $query->withTranslation(app()->getLocale())->paginate($quantity);
        $links = $products->links('partials.pagination');

        $products = $products->translate();

        return view('bestsellers', compact('page', 'breadcrumbs', 'category', 'products', 'productAllQuantity', 'links', 'quantity', 'quantityPerPage', 'sorts', 'sortCurrent'));
    }

    public function latestViewed(Request $request)
    {
        $breadcrumbs = new Breadcrumbs();

        $categoryID = $request->input('category', null);
        $category = null;
        if ($categoryID) {
            $category = Category::findOrFail($categoryID);
        }

        $page = Helper::translation(Page::findOrFail(23));
        $breadcrumbs->addItem(new LinkItem($page->name, $page->url));

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

        if ($category) {
            $query = $category->products();
            $category = $category->translate();
        } else {
            $query = Product::query();
        }

        $productAllQuantity = 0;
        $products = collect();
        $links = '';
        $latestViewedProductIDs = Cache::get('latest_viewed_products_ids');
        if ($latestViewedProductIDs) {
            $latestViewedProductIDs = explode(',', $latestViewedProductIDs);
            $query->whereIn('id', $latestViewedProductIDs);
            $query->active()->orderBy('products.' . $sort, $order);

            $productAllQuantity = $query->count();

            // get query products paginate
            $products = $query->withTranslation(app()->getLocale())->paginate($quantity);
            $links = $products->links('partials.pagination');

            $products = $products->translate();
        }

        return view('latest_viewed', compact('page', 'breadcrumbs', 'category', 'products', 'productAllQuantity', 'links', 'quantity', 'quantityPerPage', 'sorts', 'sortCurrent'));
    }

    public function promotionalProducts(Request $request)
    {
        $breadcrumbs = new Breadcrumbs();

        $categoryID = $request->input('category', null);
        $category = null;
        if ($categoryID) {
            $category = Category::findOrFail($categoryID);
        }

        $breadcrumbs->addItem(new LinkItem(__('main.promotions'), route('promotional-products')));

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
            $sortCurrent = $sorts[2];
        }
        $sortRaw = explode('-', $sortCurrent);
        $sort = $sortRaw[0];
        $order = $sortRaw[1];

        if ($category) {
            $query = $category->products();
            $category = $category->translate();
        } else {
            $query = Product::query();
        }

        $query->promotion()->active()->orderBy('products.' . $sort, $order);

        $productAllQuantity = $query->count();

        // get query products paginate
        $products = $query->withTranslation(app()->getLocale())->paginate($quantity);
        $links = $products->links();

        $products = $products->translate();

        return view('promotional_products', compact('breadcrumbs', 'category', 'products', 'productAllQuantity', 'links', 'quantity', 'quantityPerPage', 'sorts', 'sortCurrent'));
    }

    public function print(Product $product)
    {
        $locale = app()->getLocale();

        $brand = $product->brand;
        if (!empty($brand)) {
            $brand = Helper::translation($brand);
        }

        $deliveryText = Helper::translation(Helper::staticText('delivery_text'))->description;

        // attributes
        $attributeValueIds = $product->attributeValuesIds();
        $attributes = $product->attributesOrdered()->withTranslation($locale)->with(['attributeValues' => function ($q1) use ($attributeValueIds, $locale) {
            $q1->whereIn('id', $attributeValueIds)->withTranslation($locale);
        }])->get()->translate();

        $product = Helper::translation($product);

        return view('product.print', compact('product', 'brand', 'attributes', 'deliveryText'));
    }

    public function updatesaledate(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id'
        ]);

        Product::where('id', $request->id)->update([
            'sale_price' => null,
            'sale_end_date' => null,
            'is_promotion' => 0
        ]);

        return true;
    }

    public function getLocationOfUser()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $userIp = $_SERVER['HTTP_CLIENT_IP'];
        }
        else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $userIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else {
            $userIp = $_SERVER['REMOTE_ADDR'];
        }

        $ipdat = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $userIp));
        $userCity = $ipdat->geoplugin_city;
        $userCountry = $ipdat->geoplugin_countryName;

        $changedLocal = '';
        if (str_contains(url()->current(), '/uz')) {
            $changedLocal = "uz";
        }elseif (str_contains(url()->current(), '/ru')) {
            $changedLocal = "ru";
        }

        if (!empty($changedLocal)) {
            App::setLocale($changedLocal);
        }else{
            if ($userCountry == "Uzbekistan") {
                if ($userCity == "Tashkent") {
                    App::setLocale('ru');
                }else{
                    App::setLocale('uz');
                }
            } else {
                App::setLocale('ru');
            }
        }
    }
}
