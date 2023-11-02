<?php

namespace App\View\Components;

use App\Helpers\Helper;
use App\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class DayProduct extends Component
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
        $dayProductID = setting('site.day_product');
        // $productID = Cache::remember('day-product', 86400, function () {
        //     do {
        //         $prod = Product::active()->inRandomOrder()->first();
        //         if (!$prod) {
        //             return 0;
        //         }
        //     } while ($prod->in_stock == 0);
        //     return $prod->id;
        // });
        $product = Product::find($dayProductID);
        if (!$product) {
            $product = Product::active()->first();
        }

        return view('components.day_product', compact('product'));
    }
}
