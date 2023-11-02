<?php

namespace App\View\Components;

use Illuminate\View\Component;
use TCG\Voyager\Facades\Voyager;

class TopSearch extends Component
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
        $cartQuantity = app('cart')->getTotalQuantity();
        $wishlistQuantity = app('wishlist')->getTotalQuantity();
        $compareQuantity = app('compare')->getTotalQuantity();
        $siteLogo = setting('site.logo');
        $logo = $siteLogo ? Voyager::image($siteLogo) : '/img/logo.png';

        return view('components.top_search', compact('cartQuantity', 'wishlistQuantity', 'compareQuantity', 'logo'));
    }
}
