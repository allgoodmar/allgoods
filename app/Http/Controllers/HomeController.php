<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Helpers\Helper;
use App\Page;
use App\Popupbanner;
use App\Product;
use App\Publication;
use App\Region;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (!empty(auth()->user()->id)) {
            if (!empty(Session::get('voucher'))) {
                User::where('id', auth()->user()->id)->update([
                    'is_coupon_used' => 'no',
                    'voucher' => 'true',
                ]);

                $user = User::find(auth()->user()->id);
                if (!empty($user->coupon_sum)) {
                    $user->increment('coupon_sum', Session::get('voucher'));
                }else{
                    $user->coupon_sum = Session::get('voucher');
                }
                $user->save();
                Session::forget('voucher');
            }
        }
        
        // $this->getLocationOfUser();
        $locale = app()->getLocale();

        $page = Page::findOrFail(1)->translate();
        $pageAbout = ''; // Page::find(3)->translate();
        $pageDiscounted = Page::find(12)->translate();
        $pageInstallments = Page::find(5)->translate();

        // $currentRegionID = Helper::getCurrentRegionID();
        $currentRegion = Helper::getCurrentRegion();
        $warehouseIDs = $currentRegion->warehouses->pluck('id')->toArray();

        // slides
        $slides = Helper::banners('slide');
        if ($slides) {
            $slides = $slides->translate();
        }

        // $homeCategories = Helper::categories('home', 9);
        $homeCategories = collect();
        $homeCategoriesProducts = [];
        $homeCategoryIDs = setting('site.home_category_ids');
        $homeCategoryIDs = explode(',', $homeCategoryIDs);
        $homeCategoryIDs = array_map(function($v){
            return (int)$v;
        }, $homeCategoryIDs);
        if ($homeCategoryIDs) {
            $homeCategories = Category::active()
                ->withTranslation($locale)
                ->whereIn('id', $homeCategoryIDs)
                ->get()
                ->keyBy('id');
            foreach ($homeCategoryIDs as $homeCategoryID) {
                if (!empty($homeCategories[$homeCategoryID])) {
                    $homeCategory = $homeCategories[$homeCategoryID];
                    $homeCategoriesProducts[$homeCategory->id] = [
                        'category' => $homeCategory,
                        'products' => $homeCategory
                            ->getModel()
                            ->products()
                            ->active()
                            // ->latest()
                            ->where('order', '>', 0)
                            ->where('in_stock', '>', 0)
                            ->orderBy('order')
                            // ->orderByRaw('`order` = 0')
                            ->take(10)
                            ->withTranslation($locale)
                            ->with('categories')
                            ->with('installmentPlans')
                            ->get()
                            ->translate(),
                    ];
                }
            }
        }

        // $newProductsQuery = Product::active()
        //     ->new()
        //     ->with(['categories' => function($query) use ($locale) {
        //         $query->withTranslation($locale);
        //     }])
        //     ->with('installmentPlans')
        //     ->withTranslation($locale)
        //     ->orderBy('products.created_at');
        // $newProducts = $newProductsQuery->take(6)->get();
        // if (!$newProducts->isEmpty()) {
        //     $newProducts = $newProducts->translate();
        // }

        // $promotionProductsQuery = Product::active()
        //     ->promotion()
        //     ->with(['categories' => function($query) use ($locale) {
        //         $query->withTranslation($locale);
        //     }])
        //     ->with('installmentPlans')
        //     ->withTranslation($locale)
        //     ->orderBy('products.created_at');
        // $promotionProducts = $promotionProductsQuery->take(6)->get();
        // if (!$promotionProducts->isEmpty()) {
        //     $promotionProducts = $promotionProducts->translate();
        // }

        // $latestViewedProducts = collect();
        // $latestViewedProductIDs = Cache::get('latest_viewed_products_ids');
        // $latestViewedProductIDs = explode(',', $latestViewedProductIDs);
        // if (count($latestViewedProductIDs)) {
        //     $latestViewedProductIDs = array_slice($latestViewedProductIDs, 0, 6);
        //     $latestViewedProducts = Product::active()
        //         ->with(['categories' => function($query) use ($locale) {
        //             $query->withTranslation($locale);
        //         }])
        //         ->with('installmentPlans')
        //         ->withTranslation($locale)
        //         ->whereIn('id', $latestViewedProductIDs)
        //         ->get();
        // }
        // if (!$latestViewedProducts->isEmpty()) {
        //     $latestViewedProducts = $latestViewedProducts->translate();
        // }

        // product blocks
        // $productsBlocksCategories = Category::active()->whereIn('id', [1, 11, 23])->withTranslation($locale)->get();
        // $productsBlocks = [];
        // foreach ($productsBlocksCategories as $productsBlocksCategory) {
        //     $productsBlock = [
        //         'category' => $productsBlocksCategory,
        //         'products' => collect(),
        //     ];
        //     $query = $productsBlocksCategory->products()
        //         ->active()
        //         ->with(['categories' => function($query) use ($locale) {
        //             $query->withTranslation($locale);
        //         }])
        //         ->withTranslation($locale)
        //         ->orderBy('products.created_at');
        //     $productsBlockProducts = $query->take(12)->get();
        //     if (!$productsBlockProducts->isEmpty()) {
        //         $productsBlockProducts = $productsBlockProducts->translate();
        //     }
        //     $productsBlock['products'] = $productsBlockProducts;
        //     $productsBlocks[] = $productsBlock;
        // }

        // articles
        $articles = Publication::articles()->active()->latest()->take(4)->get();
        if (!$articles->isEmpty()) {
            $articles = $articles->translate();
        }

        $popupbanner = '';
        $popupbannerId = Popupbanner::orderBy('created_at', 'DESC')->first();
        if (!empty($popupbannerId)) {
            $popupbanner = Product::where('id', $popupbannerId->product_id)->first();
        }

        return view('home', compact('page', 'pageAbout', 'pageDiscounted', 'pageInstallments', 'slides', 'homeCategoriesProducts', 'articles', 'popupbanner'));
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

    public function latestProducts(Category $category)
    {
        $products = $category->allProducts()->active()->latest()->take(10)->get()->translate();
        $category = $category->translate();
        return view('partials.latest_products_slider', compact('category', 'products'));
    }
}
