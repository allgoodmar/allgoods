<?php

namespace App\View\Components;

use App\Helpers\Helper;
use App\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class DiscountedProducts extends Component
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
        $discountedProducts = Cache::remember(config('params.discountedProducts') . '_' . $locale, 3600, function () use ($locale) {
            $discountedProductsQuery = Product::active()
                ->discounted()
                ->with(['categories' => function ($query) use ($locale) {
                    $query->withTranslation($locale);
                }])
                ->with('installmentPlans')
                ->withTranslation($locale)
                ->orderBy('products.order');
            $discountedProducts = $discountedProductsQuery->take(6)->get();
            if (!$discountedProducts->isEmpty()) {
                $discountedProducts = $discountedProducts->translate();
            }
            return $discountedProducts;
        });
        return view('components.discounted_products', compact('discountedProducts'));
    }
}
