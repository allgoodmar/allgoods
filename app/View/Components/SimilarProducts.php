<?php

namespace App\View\Components;

use App\Helpers\Helper;
use App\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class SimilarProducts extends Component
{
    public $productId;

    public function __construct($productId)
    {
        $this->productId = $productId;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        $locale = app()->getLocale();
        $product = Product::findOrFail($this->productId);
        $products = $product->similar()
            ->active()
            ->with(['categories' => function($q) use ($locale) {
                $q->withTranslation($locale);
            }])
            // ->with('installmentPlans')
            ->latest()
            ->withTranslation($locale)
            ->take(6)
            ->get();

        return view('components.similar_products', compact('products'));
    }
}
