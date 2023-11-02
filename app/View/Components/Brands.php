<?php

namespace App\View\Components;

use App\Brand;
use App\Helpers\Helper;
use App\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class Brands extends Component
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
        $brands = Brand::active()->take(12)->orderBy('order', 'ASC')->get();
        if (!$brands->isEmpty()) {
            $brands = $brands->translate();
        }
        return view('components.brands', compact('brands'));
    }
}
