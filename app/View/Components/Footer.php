<?php

namespace App\View\Components;

use App\Helpers\Helper;
use App\Helpers\LinkItem;
use App\Page;
use App\Region;
use App\StaticText;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\Component;
use TCG\Voyager\Facades\Voyager;

class Footer extends Component
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

        $pages = Page::active()->whereIn('id', [1, 2, 3, 4, 8, 9 ])->withTranslation($locale)->get()->keyBy('id');
        // $pages = Page::active()->inFooterMenu()->withTranslation($locale)->get()->keyBy('id');

        $footerMenuItemsMain = Helper::menuItems('footer');
        $footerMenuItems = [];
        $footerMenuItemsIDs = [
            [1, 2, 3, 4, 8, 9 ],
            // [5, 6, 8, 9, 16],
        ];
        foreach($footerMenuItemsIDs as $key => $value) {
            foreach($value as $footerMenuItemsID) {
                if (isset($pages[$footerMenuItemsID])) {
                    $page = $pages[$footerMenuItemsID]->translate();
                    $footerMenuItems[$key][] = new LinkItem($page->name, $page->url);
                }
            }
        }

        $siteLogo = setting('site.logo');
        $siteLightLogo = setting('site.logo_light');
        $logo = $siteLogo ? Voyager::image($siteLogo) : '/img/logo.png';
        $logoLight = $siteLightLogo ? Voyager::image($siteLightLogo) : '/img/logo.png';

        $address = StaticText::where('key', 'contact_address')->first()->translate()->description;
        $workHours = StaticText::where('key', 'work_hours')->first()->translate()->description;

        $categories = Helper::categories();

        $currentRegionID = Helper::getCurrentRegionID();
        $regions = Cache::remember('regions', 86400, function () use ($locale) {
            $regions = Region::orderBy('short_name')->withTranslation($locale)->get();
            if ($regions) {
                $regions = $regions->translate();
            }
            return $regions;
        });

        $cartQuantity = app('cart')->getTotalQuantity();
        $wishlistQuantity = app('wishlist')->getTotalQuantity();
        $compareQuantity = app('compare')->getTotalQuantity();

        return view('components.footer', compact('footerMenuItems', 'footerMenuItemsMain', 'pages', 'logo', 'logoLight', 'address', 'workHours', 'categories', 'currentRegionID', 'regions', 'cartQuantity', 'wishlistQuantity', 'compareQuantity'));
    }
}
