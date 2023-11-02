<?php

namespace App\Http\Controllers\Api\V2;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Product;
use App\ShippingMethod;
use Darryldecode\Cart\CartCondition;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartData = $this->getData();
        return response()->json($cartData);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $locale = app()->getLocale();
        $cart = app('cart');

        $errors = [];
        $ids = collect($data['products'])->pluck('id');
        $products = Product::withTranslation($locale)->with('brand')->whereIn('id', $ids)->get();
        foreach ($products as $product) {
            if ($product->getStock() < 1) {
                $errors['products.' . $product->id . '.quantity'] = [
                    __('main.product_is_out_of_stock'),
                ];
            }
        }

        if (count($errors)) {
            return response()->json([
                'message' => __('The given data was invalid.'),
                'errors' => $errors,
            ], 422);
        }

        // clear cart
        $cart->clearCartConditions();
        $cart->clear();

        // add items to cart
        foreach ($data['products'] as $value) {
            $product = $products->where('id', $value['id'])->first();
            $cart->add([
                'id' => $product->id,
                'name' => $product->getTranslatedAttribute('name'),
                'price' => $product->current_not_sale_price,
                'quantity' => $value['quantity'],
                'associatedModel' => $product,
            ]);

            // conditions
            $discount = $product->current_not_sale_price - $product->current_price;
            $saleConditionName = config('shopping_cart.item_sale_condition_prefix') . $product->id;
            $cart->removeItemCondition($product->id, $saleConditionName);
            if ($discount > 0) {
                $condition = new CartCondition([
                    'name' => $saleConditionName,
                    'type' => 'sale',
                    'value' => '-' . $discount,
                ]);
                $cart->addItemCondition($product->id, $condition);
            }
        }

        $cartData = $this->getData();
        return response()->json($cartData);
        // return response()->json([
        //     'message' => __('main.cart_updated'),
        // ]);
    }

    public function clear()
    {
        $cart = app('cart');
        $cart->clearCartConditions();
        $cart->clear();

        return response()->json([
            'message' => __('main.cart_has_been_cleared'),
        ]);
    }

    public function itemsAdd(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $locale = app()->getLocale();
        $cart = app('cart');
        $product = Product::withTranslation($locale)->where('id', $data['product_id'])->first();

        // update stock if trendyol
        if ($product->isTrendyolProduct()) {
            Helper::trendyolUpdateStock([$product->barcode]);
            $product->refresh();
        }

        // check stock
        if ($product->getStock() < 1) {
            return response()->json([
                'message' => __('The given data was invalid.'),
                'errors' => [
                    'quantity' => [
                        __('main.product_is_out_of_stock'),
                    ],
                ],
            ], 422);
        }

        // add item
        $cart->add([
            'id' => $product->id,
            'name' => $product->getTranslatedAttribute('name'),
            'price' => $product->current_not_sale_price,
            'quantity' => $data['quantity'],
            'associatedModel' => $product,
        ]);

        // conditions
        $discount = $product->current_not_sale_price - $product->current_price;
        $saleConditionName = config('shopping_cart.item_sale_condition_prefix') . $product->id;
        $cart->removeItemCondition($product->id, $saleConditionName);
        if ($discount > 0) {
            $condition = new CartCondition([
                'name' => $saleConditionName,
                'type' => 'sale',
                'value' => '-' . $discount,
            ]);
            $cart->addItemCondition($product->id, $condition);
        }

        return response()->json([
            'message' => __('main.product_added_to_cart'),
        ]);
    }

    public function itemsUpdate(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $locale = app()->getLocale();
        $cart = app('cart');

        $item = $cart->get($data['product_id']);
        if (!$item) {
            return response()->json([
                'message' => __('main.error'),
                'errors' => [
                    'product_id' => [
                        __('main.product_is_not_in_cart'),
                    ],
                ],
            ], 422);
        }

        $product = Product::withTranslation($locale)->where('id', $data['product_id'])->first();

        // update stock if trendyol
        if ($product->isTrendyolProduct()) {
            Helper::trendyolUpdateStock([$product->barcode]);
            $product->refresh();
        }

        // check stock
        if ($product->getStock() < 1) {
            return response()->json([
                'message' => __('The given data was invalid.'),
                'errors' => [
                    'quantity' => [
                        __('main.product_is_out_of_stock'),
                    ],
                ],
            ], 422);
        }

        // update items
        $cart->update($product->id, [
            'price' => $product->current_not_sale_price,
            'quantity' => [
                'relative' => false,
                'value' => $data['quantity'],
            ],
        ]);

        // conditions
        $discount = $product->current_not_sale_price - $product->current_price;
        $saleConditionName = config('shopping_cart.item_sale_condition_prefix') . $product->id;
        $cart->removeItemCondition($product->id, $saleConditionName);
        if ($discount > 0) {
            $condition = new CartCondition([
                'name' => $saleConditionName,
                'type' => 'sale',
                'value' => '-' . $discount,
            ]);
            $cart->addItemCondition($product->id, $condition);
        }

        return response()->json([
            'message' => __('main.cart_updated'),
        ]);
    }

    public function itemsRemove(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required',
        ]);

        $cart = app('cart');
        $cart->clearItemConditions($data['product_id']);
        $cart->remove($data['product_id']);

        return response()->json([
            'message' => __('main.product_removed_from_cart'),
        ]);
    }

    public function itemsRemoveMultiple(Request $request)
    {
        $data = $request->validate([
            'product_ids' => 'required|array',
        ]);

        $cart = app('cart');
        foreach ($data['product_ids'] as $id) {
            $cart->clearItemConditions($id);
            $cart->remove($id);
        }

        return response()->json([
            'message' => __('main.product_removed_from_cart'),
        ]);
    }

    private function getData()
    {
        $cart = app('cart');
        $items = $cart->getContent()->sortBy('id');
        $cartSubtotal = $cart->getSubTotalWithoutConditions();
        $shippingMethod = ShippingMethod::active()->orderBy('order')->first();
        $shippingPrice = $shippingMethod ? $shippingMethod->price : 0;
        $cartTotal = $cart->getTotal();
        $discount = $cartSubtotal - $cartTotal;
        $subtotal = $cartSubtotal;
        $total = $cartTotal + $shippingPrice;
        $data = [
            'quantity' => $cart->getTotalQuantity(),
            'subtotal' => floatval($subtotal),
            'subtotal_formatted' => Helper::formatPrice($subtotal),
            'total' => floatval($total),
            'total_formatted' => Helper::formatPrice($total),
            'discount' => floatval($discount),
            'discount_formatted' => Helper::formatPrice($discount),
            'delivery' => floatval($shippingPrice),
            'delivery_formatted' => Helper::formatPrice($shippingPrice),
            'items' => [],
        ];
        foreach ($items as $item) {
            $currentPrice = floatval($item->getPriceWithConditions());
            $oldPrice = floatval(round($item->price, 2));
            $lineSubtotal = floatval($item->getPriceSum());
            $lineTotal = floatval($item->getPriceSumWithConditions());
            $data['items'][] = [
                'id' => intval($item->id),
                'name' => $item->name,
                'current_price' => $currentPrice,
                'current_price_formatted' => Helper::formatPrice($currentPrice),
                'old_price' => $oldPrice,
                'old_price_formatted' => Helper::formatPrice($oldPrice),
                'quantity' => intval($item->quantity),
                'line_subtotal' => $lineSubtotal,
                'line_subtotal_formatted' => Helper::formatPrice($lineSubtotal),
                'line_total' => $lineTotal,
                'line_total_formatted' => Helper::formatPrice($lineTotal),
                'product' => new ProductResource($item->associatedModel),
            ];
        }
        return $data;
    }
}
