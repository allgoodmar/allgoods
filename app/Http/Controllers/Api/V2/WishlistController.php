<?php

namespace App\Http\Controllers\Api\V2;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $wishlistData = $this->getData();
        return response()->json($wishlistData);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
        ]);

        $locale = app()->getLocale();
        $wishlist = app('wishlist');

        $ids = collect($data['products'])->pluck('id');
        $products = Product::withTranslation($locale)->with('brand')->whereIn('id', $ids)->get();

        // clear wishlist
        $wishlist->clear();

        // add items to wishlist
        foreach ($data['products'] as $value) {
            $product = $products->where('id', $value['id'])->first();
            $wishlist->add([
                'id' => $product->id,
                'name' => $product->getTranslatedAttribute('name'),
                'price' => $product->current_not_sale_price,
                'quantity' => 1,
                'associatedModel' => $product,
            ]);
        }

        $wishlistData = $this->getData();
        return response()->json($wishlistData);
    }

    public function clear()
    {
        $wishlist = app('wishlist');
        $wishlist->clear();

        return response()->json([
            'message' => __('main.wishlist_has_been_cleared'),
        ]);
    }

    public function itemsAdd(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $locale = app()->getLocale();
        $wishlist = app('wishlist');

        if (!$wishlist->get($data['product_id'])) {
            // get product
            $product = Product::withTranslation($locale)->where('id', $data['product_id'])->first();

            // add item
            $wishlist->add([
                'id' => $product->id,
                'name' => $product->getTranslatedAttribute('name'),
                'price' => $product->current_price,
                'quantity' => 1,
                'associatedModel' => $product,
            ]);
        }

        return response()->json([
            'message' => __('main.product_added_to_wishlist'),
        ]);
    }

    public function itemsRemove(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required',
        ]);

        $wishlist = app('wishlist');
        $wishlist->remove($data['product_id']);

        return response()->json([
            'message' => __('main.product_removed_from_wishlist'),
        ]);
    }

    public function itemsRemoveMultiple(Request $request)
    {
        $data = $request->validate([
            'product_ids' => 'required|array',
        ]);

        $wishlist = app('wishlist');
        foreach ($data['product_ids'] as $id) {
            $wishlist->remove($id);
        }

        return response()->json([
            'message' => __('main.product_removed_from_wishlist'),
        ]);
    }

    private function getData()
    {
        $wishlist = app('wishlist');
        $items = $wishlist->getContent()->sortBy('id');
        $data = [
            'quantity' => $wishlist->getTotalQuantity(),
            'items' => [],
        ];
        foreach ($items as $item) {
            $data['items'][] = [
                'id' => $item->id,
                'name' => $item->name,
                'product' => new ProductResource($item->associatedModel),
            ];
        }
        return $data;
    }
}
