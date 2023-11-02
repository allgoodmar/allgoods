<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Helpers\Breadcrumbs;
use App\Helpers\Helper;
use App\Helpers\LinkItem;
use App\Page;
use App\Product;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * show products per page values
     */
    public $quantityPerPage = [30, 60, 120];
    public $sorts = ['views-desc', 'created_at-desc', 'price-asc', 'price-desc', 'rating-desc'];

    public function index()
    {
        $breadcrumbs = new Breadcrumbs();
        $page = Helper::translation(Page::findOrFail(16));
        $breadcrumbs->addItem(new LinkItem($page->name, $page->url, LinkItem::STATUS_INACTIVE));

        $brands = Brand::active()->withTranslation(app()->getLocale())->get()->translate();

        return view('brands.index', compact('page', 'breadcrumbs', 'brands'));
    }

    public function show(Request $request, Brand $brand)
    {
        $breadcrumbs = new Breadcrumbs();

        $locale = app()->getLocale();

        $page = Helper::translation(Page::findOrFail(16));
        $breadcrumbs->addItem(new LinkItem($page->name, $page->url));

        $currentRegion = Helper::getCurrentRegion();
        $warehouseIDs = $currentRegion->warehouses->pluck('id')->toArray();

        // quantity per page
        $quantityPerPage = $this->quantityPerPage;
        $quantity = $request->input('quantity', $this->quantityPerPage[0]);
        if (!in_array($quantity, $this->quantityPerPage)) {
            $quantity = $this->quantityPerPage[0];
        }

        // sort - order
        $sorts = $this->sorts;
        $sortCurrent = $request->input('sort', '');
        if (empty($sortCurrent) || !in_array($sortCurrent, $sorts)) {
            $sortCurrent = $sorts[0];
        }
        $sortRaw = explode('-', $sortCurrent);
        $sort = $sortRaw[0];
        $order = $sortRaw[1];

        $query = $brand->products()
            ->orderBy('products.' . $sort, $order)
            ->active()
            ->with(['categories' => function($query) use ($locale) {
                $query->withTranslation($locale);
            }])
            ->withTranslation($locale)
            ->orderBy('products.created_at');

        $productAllQuantity = $query->count();

        // get query products paginate
        $products = $query->paginate($quantity);
        $links = $products->links('partials.pagination');
        $links = $products->appends(['sort' => $sortCurrent])->links();

        if (!$products->isEmpty()) {
            $products = $products->translate();
        }

        $brandCategories = Category::active()
            ->whereNotNull('parent_id')
            ->whereHas('products', function($q1) use ($brand) {
                $q1->active()->where('brand_id', $brand->id);
            })
            ->whereHas('parent', function($q2) {
                $q2->whereNull('parent_id');
            })
            ->with('parent', function($q3) use ($locale) {
                $q3->withTranslation($locale);
            })
            ->withTranslation($locale)
            ->get();

        $brand = Helper::translation($brand);

        $breadcrumbs->addItem(new LinkItem($brand->name, $brand->url, LinkItem::STATUS_INACTIVE));

        return view('brands.show', compact('brandCategories', 'page', 'breadcrumbs', 'products', 'productAllQuantity', 'brand', 'links', 'quantity', 'quantityPerPage', 'sorts', 'sortCurrent'));
    }

}
