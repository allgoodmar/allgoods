<?php

namespace App\View\Components;

use App\Helpers\Helper;
use App\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class BestsellerProducts extends Component
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
        $bestsellerProducts = Cache::remember(config('params.bestsellerProducts') . '_' . $locale, 3600, function () use ($locale) {
            $bestsellerProductsQuery = Product::active()
                ->bestseller()
                ->with(['categories' => function ($query) use ($locale) {
                    $query->withTranslation($locale);
                }])
                ->with('installmentPlans')
                ->withTranslation($locale)
                ->orderBy('products.order');
            $bestsellerProducts = $bestsellerProductsQuery->take(6)->get();
            if (!$bestsellerProducts->isEmpty()) {
                $bestsellerProducts = $bestsellerProducts->translate();
            }
            return $bestsellerProducts;
        });
        return view('components.bestseller_products', compact('bestsellerProducts'));
    }
}
