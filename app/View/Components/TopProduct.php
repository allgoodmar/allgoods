<?php

namespace App\View\Components;

use App\Product;
use Illuminate\View\Component;

class TopProduct extends Component
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
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $locale = app()->getLocale();
        $product = Product::query();

        $products = $product
            ->active()
            ->where('views', '>', 900)
            ->withTranslation($locale)
            ->inRandomOrder();
        $products = $products->limit(8)->get();

        return view('components.top-product', compact('products'));
    }
}
