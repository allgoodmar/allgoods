<?php

namespace App\View\Components;

use App\Brand;
use App\Category;
use App\Helpers\Helper;
use App\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class Categories extends Component
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

        // $categories = Helper::categories('home', 9);

        $categories = collect();
        $ids = setting('site.catalog_category_ids');
        $ids = explode(',', $ids);
        $ids = array_map(function($v){
            return (int)$v;
        }, $ids);
        if ($ids) {
            $categories = Category::active()
                ->withTranslation($locale)
                ->whereIn('id', $ids)
                ->get()
                ->keyBy('id');
        }

        return view('components.categories', compact('categories', 'ids'));
    }
}
