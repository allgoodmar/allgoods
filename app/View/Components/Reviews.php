<?php

namespace App\View\Components;

use App\Review;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class Reviews extends Component
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

        $reviews =   Cache::remember('reviews_first'.'_'.app()->getLocale(), config('params.reviews_first'), function () {
            return  Review::select(
                "reviews.*",
                DB::raw("products.name as product_name"),
                DB::raw("products.id as product_id"),
                DB::raw("products.slug as product_slug"),
                DB::raw("products.image as product_image"),
                DB::raw("LENGTH(reviews.body) as length")
            )
                // ->where('length', '>=', '40')
                ->whereRaw('LENGTH(reviews.body) >= 50')
                ->join('products', 'products.id', '=', 'reviews.reviewable_id')
                ->inRandomOrder()
                ->limit(8)
                ->get();
        });

        $reviews2 =   Cache::remember('reviews_second'.'_'.app()->getLocale(), config('params.reviews_second'), function (){
            return  Review::select(
                "reviews.*",
                DB::raw("products.name as product_name"),
                DB::raw("products.id as product_id"),
                DB::raw("products.slug as product_slug"),
                DB::raw("products.image as product_image"),
                DB::raw("LENGTH(reviews.body) as length")
            )
                // ->where('length', '>=', '40')
                ->whereRaw('LENGTH(reviews.body) >= 50')
                ->join('products', 'products.id', '=', 'reviews.reviewable_id')
                ->inRandomOrder()
                ->limit(8)
                ->get();
        });

        $first = [];
        $second = [];
        $reviewIDs = Review::select('id')->get();

        return view('components.reviews', compact('reviews', 'reviews2'));
    }
}
