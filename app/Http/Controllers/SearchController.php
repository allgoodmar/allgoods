<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Helpers\Breadcrumbs;
use App\Helpers\Helper;
use App\Helpers\LinkItem;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Page;
use App\Product;
use App\Search;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        if (!preg_match('/^[a-zA-Z0-9\s]*$/', $request->input('q', ''))) {
            return redirect()->back();
        }
        $locale = app()->getLocale();
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.nav.search'), route('search'), LinkItem::STATUS_INACTIVE));

        $q = Helper::escapeFullTextSearch($request->input('q', ''));
        if (!preg_match('/^[a-zA-Z0-9\s]*$/', $q)) {
            return redirect()->back();
        }

        $isJson = $request->input('json', '');

        // $searches = collect([]);

        // MULTISEARCH STARTS HERE

        $data = Http::get("https://api.multisearch.io/?id=12222&query=".$q."&uid=cc441f080&limit=10000&offset=0&categories=0&fields=id");
        $items = json_decode($data->body(), true);

        $idsForSearch = [];
        foreach ($items['results']['items'] as $item)
        {
            $idsForSearch[] = $item['id'];
        }

        // MULTISEACH ENDS HERE

        if ($q && Str::length($q) >= 3) {
            $searches = Product::whereIn('id', $idsForSearch)
            ->where('in_stock', '>=', 1)
            ->orderBy('id', 'DESC')
            ->paginate(30);
        }

        /*
        if ($q && Str::length($q) >= 3) {
            $searches = Product::where([['name', 'like', '%' . $q . '%'],['status', 1]])
            ->orWhere([['body', 'like', '%' . $q . '%'],['status', 1]])
            ->orderBy('views', 'DESC')
            ->paginate(30);
        }
        */

        $links = $searches->appends(['q' => $q])->links('partials.pagination');

        return view('search', compact('breadcrumbs', 'searches', 'links', 'q'));
    }

    public function indexNew(Request $request)
    {
        $locale = app()->getLocale();
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.nav.search'), route('search'), LinkItem::STATUS_INACTIVE));

        $categories = null;
        $brands = null;
        $products = null;
        $links = '';

        $q = htmlspecialchars($request->input('q', ''));
        $isJson = $request->input('json', '');
        if ($q && Str::length($q) >= 3) {
            $categories = Category::search($q)->paginate(30);
            if (!$categories->isEmpty()) {
                $categories = $categories->translate();
            }
            $brands = Brand::search($q)->paginate(30);
            if (!$brands->isEmpty()) {
                $brands = $brands->translate();
            }
            $products = Product::search($q)->paginate(30);
            if (!$products->isEmpty()) {
                $links = $products->links('partials.pagination');
            }
        }

        if ($isJson) {
            return [
                'q' => $q,
                'products' => ProductResource::collection($products ?? collect()),
                'categories' => CategoryResource::collection($categories ?? collect()),
                'brands' => BrandResource::collection($brands ?? collect()),
            ];
        }

        return view('search_new', compact('breadcrumbs', 'categories', 'brands', 'products', 'links', 'q'));
    }

    public function ajax(Request $request)
    {
        $results = [];
        $q = $this->getQ();
        $searches = $this->getSearches($q, 50);
        foreach ($searches as $item) {
            $results[] = [
                'name' => $item->searchable->getTranslatedAttribute('name') ?? $item->searchable->getTranslatedAttribute('full_name'),
                'url' => $item->searchable->url,
            ];
        }

        return $results;
    }

    private function getSearches($q, $quantity = 0)
    {
        $searches = collect([]);
        if ($quantity == 0) {
            $quantity = $this->perPage;
        }

        if ($q && Str::length($q) >= 3) {

            $searches = Search::where('body', 'like', '%' . $q . '%')
                ->with(['searchable' => function($q1) {
                    $q1->withTranslation(app()->getLocale());
                }])
                ->paginate($quantity);

            if ($searches->isEmpty()) {
                $qArray = explode(' ', $q);
                if (count($qArray) > 0) {
                    $searches = Search::where(function ($query) use ($qArray) {
                        foreach ($qArray as $qWord) {
                            if (mb_strlen($qWord) > 2) {
                                $query->orWhere('body', 'like', '%' . $qWord . '%');
                            }
                        }
                    })
                        ->with(['searchable' => function($q1) {
                            $q1->withTranslation(app()->getLocale());
                        }])
                        ->paginate($quantity);
                }
            }
        }

        return $searches;
    }
}
