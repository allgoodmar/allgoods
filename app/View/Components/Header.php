<?php

namespace App\View\Components;

use App\Category;
use App\Helpers\Helper;
use App\Page;
use App\Region;
use App\StaticText;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\Component;
use TCG\Voyager\Facades\Voyager;

class Header extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $locale = app()->getLocale();

        // $banner = Helper::banner('top_1');
        // $menuItems = Helper::menuItems();
        $headerMenuItems = Helper::menuItems('header');
        $pageContact = Page::find(2)->translate();
        $siteLogo = setting('site.logo');
        $logo = $siteLogo ? Voyager::image($siteLogo) : '/img/logo.png';
        $siteLogoLight = setting('site.logo_light');
        $logoLight = $siteLogoLight ? Voyager::image($siteLogoLight) : '/img/logo-light.png';

        $switcher = Helper::languageSwitcher();
        $activeLanguageRegional = Helper::getActiveLanguageRegional();

        $address = StaticText::where('key', 'contact_address')->first()->translate()->description;
        $workHours = StaticText::where('key', 'work_hours')->first()->translate()->description;

        $cartQuantity = app('cart')->getTotalQuantity();
        $wishlistQuantity = app('wishlist')->getTotalQuantity();
        $compareQuantity = app('compare')->getTotalQuantity();

        $categories = Helper::categories('parents');
        $menuCategoryBanners =   Cache::remember(count($categories).'_cache_'.app()->getLocale(), config('params.menuCategoryBanners'), function () use ($categories) {
            $menuCategoryBanners = collect();
            foreach ($categories as $category) {
                $categoryBanner = $category->getModel()->banners()->menuCategory()->latest()->active()->first();
                if ($categoryBanner) {
                    $categoryBanner = Helper::translation($categoryBanner);
                    $menuCategoryBanners->put($category->id, $categoryBanner);
                }
            }
            return $menuCategoryBanners;
        });


        $menuCategories = Helper::categories('menu');

        // $issetRegionID = Cookie::get('region_id');
        // $currentRegion = Helper::getCurrentRegion();

        $q = request('q', '');

        $badEye = json_decode(request()->cookie('bad_eye'), true);
        if (!$badEye) {
            $badEye = [
                'font_size' => 'normal',
                'contrast' => 'normal',
                'images' => 'normal',
            ];
        }

        return view('components.header', compact('headerMenuItems', 'categories', 'menuCategoryBanners', 'menuCategories', 'cartQuantity', 'wishlistQuantity', 'compareQuantity', 'pageContact', 'logo', 'logoLight', 'switcher', 'activeLanguageRegional', 'q', 'address', 'workHours', 'badEye'));
    }
}
