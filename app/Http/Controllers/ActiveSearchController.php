<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Helpers\Helper;
use App\MySearch;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActiveSearchController extends Controller
{
    public function searchindex(Request $request)
    {

        /*$products = Product::active()->translated()->take(6)->get();

            dd($products);*/
        return view('test.search');

    }

    public function search(Request $request)
    {

        $locale = app()->getLocale();

        if($request->ajax()) {

            $output = '';
            $output_products = '';
            $output_brands = '';
            $output_categories = '';

            //$products = Product::select("products.*")->active()->translated()->where('translations.value', 'LIKE', '%'.$request->search.'%')->take(6)->get();

            $brands = Brand::active()->withTranslation($locale)->where('name', 'LIKE', '%'.$request->search.'%')->take(6)->get();
            $categories = Category::active()->withTranslation($locale)->where('name', 'LIKE', '%'.$request->search.'%')->take(6)->get();

            $products = Product::where([['name', 'LIKE', '%'.$request->search.'%'],['status', 1]])
            ->take(6)->orderBy('views', 'DESC')->get();

            //$brands = Brand::where('name', 'LIKE', '%'.$request->search.'%')->take(4)->get();

            //$categories = Category::where('name', 'LIKE', '%'.$request->search.'%')->take(4)->get();

            // $showImg

            if($products) {

                foreach($products as $product) {

                    if (!isset($size)) {
                        $size = 'small';
                    }
                    $showImg = $size . '_img';

                    $output_products .=
                    '<a href="' . $product->url . '" class="list-group-item lh-125" title="' . $product->name . '">
                        <img src="' . $product->$showImg . '" alt="' . $product->name . '">
                        <div class="row" class="d-block">
                            <div class="d-block">
                            ' . $product->name . ' <br>
                            </div>
                            <div class="d-block">
                                <strong>' . Helper::formatPrice($product->price) . '</strong>
                            </div>
                        </div>
                    </a>';
                }

            }

            if($brands) {
                foreach($brands as $product) {

                    if (!isset($size)) {
                        $size = 'small';
                    }
                    $showImg = $size . '_img';

                    $output_brands .=
                    '
                        <a href="' . $product->url . '" class="list-group-item" title="' . $product->name . '"><img src="' . $product->$showImg . '" alt="' . $product->name . '">' . $product->name . '</a>
                    ';

                }
            }

            if($categories) {
                foreach($categories as $product) {

                    if (!isset($size)) {
                        $size = 'small';
                    }
                    $showImg = $size . '_img';

                    $output_categories .=
                    '
                        <a href="' . $product->url . '" class="list-group-item" title="' . $product->name . '"><img src="' . $product->$showImg . '" alt="' . $product->name . '">' . $product->name . '</a>
                    ';

                }
            }

            $output .=
            '<div class="row">
                <div class="col-lg-4 border-right">
                <b>' . __("main.products") . '</b>
                    <div class="products-list-group list-group">'
                   . $output_products .
                '</div>
            </div>
            ' .
            '
                <div class="col-lg-4 border-right">
                <b>' . __("main.brands") . '</b>
                    <div class="brands-list-group list-group">'
                   . $output_brands .
                '</div>
            </div>' .
            '
                <div class="col-lg-4 border-right">
                <b>' . __("main.categories") . '</b>
                    <div class="brands-list-group list-group">'
                   . $output_categories .
                '</div>
            </div>'
            . '</div>';
            return response()->json($output);

        }

        return view('test.search');

    }

    public function addmysearch(Request $request)
    {
        $request->validate([
            'search' => 'required'
        ]);

        $check = MySearch::where('search_text', $request->search)->first();
        if (!empty($check)) {
            MySearch::where('search_text', $request->search)->update([
                'count' => DB::raw("count + 1")
            ]);

            return true;
        }else{
            MySearch::create([
                'search_text' => $request->search
            ]);

            return true;
        }
    }
}
