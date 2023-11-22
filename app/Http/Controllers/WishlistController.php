<?php

namespace App\Http\Controllers;

use App\Helpers\Breadcrumbs;
use App\Helpers\Helper;
use App\Helpers\LinkItem;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WishlistController extends Controller
{
    public function index()
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.wishlist'), route('wishlist.index')));
        $wishlist = app('wishlist');
        $wishlistItems = $wishlist->getContent()->sortBy('id');

        return view('wishlist', compact('breadcrumbs', 'wishlist', 'wishlistItems'));
    }

    public function add(Request $request)
    {
        $this->clearWishlistCache();
        $data = $request->validate([
            'id' => 'required|exists:products,id',
            'name' => 'required',
            'price' => 'required|numeric|min:0'
        ]);

        $data['quantity'] = 1;
        $data['associatedModel'] = Product::findOrFail($request->input('id'));

        if (
            $data['associatedModel']->current_price != $data['price']
			// || trim($data['associatedModel']->name) != trim($data['name'])
        ) {
            abort(400);
        }

        app('wishlist')->add($data);

        return response([
            'wishlist' => $this->getWishlistInfo(app('wishlist')),
            'message' => __('main.product_added_to_wishlist'),
        ], 201);
    }

    public function delete($id)
    {
        $this->clearWishlistCache();
        app('wishlist')->remove($id);
        return response(array(
            'wishlist' => $this->getWishlistInfo(app('wishlist')),
            'message' => __('main.product_removed_from_wishlist')
        ), 200);
    }

    private function getWishlistInfo($wishlist)
    {
        $this->clearWishlistCache();
        $subtotal = $wishlist->getSubtotal();
        $total = $wishlist->getTotal();
        return [
            'quantity' => $wishlist->getTotalQuantity(),
            'subtotal' => $subtotal,
            'subtotalFormatted' => Helper::formatPrice($subtotal),
            'total' => $total,
            'totalFormatted' => Helper::formatPrice($total),
        ];
    }

    public function clearWishlistCache()
    {
        $key = request()->cookie('wishlist_session_key', Str::random(30));
        Cache::forget($key.'_cart_items'. '_' . app()->getLocale());
    }
}
