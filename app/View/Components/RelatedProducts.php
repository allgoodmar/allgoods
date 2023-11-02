<?php

namespace App\View\Components;

use App\Product;
use Illuminate\View\Component;

class RelatedProducts extends Component
{
    public $productId;

    public function __construct($productId)
    {
        $this->productId = $productId;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {

        $locale = app()->getLocale();
        $product = Product::findOrFail($this->productId);

        $products = $product
            ->related()
            ->active()
            ->withTranslation($locale)
            ->inRandomOrder();
        $products = $products->get();

        return view('components.related-products', compact('products'));
    }
}
