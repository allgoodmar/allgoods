<?php

namespace App\Providers;

use App\Product;
use App\Storages\CartStorage;
use Darryldecode\Cart\Cart;
use Darryldecode\Cart\CartCondition;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('cart', function($app)
        {
            $storage = new CartStorage();
            $events = $app['events'];
            $instanceName = 'cart';

            if (auth()->check()) {
                // user cart
                $sessionKey = auth()->user()->id;
            } else {
                // cookie cart
                $sessionKey = request()->cookie('cart_session_key', Str::random(30));
                if (Str::length($sessionKey) > 30) {
                    $sessionKey = Str::substr($sessionKey, 0, 30);
                }
                if (!Cookie::has('cart_session_key')) {
                    Cookie::queue('cart_session_key', $sessionKey, 1440 * 30);
                }
            }

            $config = config('shopping_cart');

            // new cart
            $cart = new Cart(
                $storage,
                $events,
                $instanceName,
                $sessionKey,
                $config
            );

            // if user logged in check cookie cart
            $cookieSessionKey = Cookie::get('cart_session_key');
            if (Str::length($cookieSessionKey) > 30) {
                $cookieSessionKey = Str::substr($cookieSessionKey, 0, 30);
            }
            if (auth()->check() && $cookieSessionKey) {
                $oldCart = new Cart(
                    $storage,
                    $events,
                    $instanceName,
                    $cookieSessionKey,
                    $config
                );
                // add cookie cart items to user cart
                foreach($oldCart->getContent() as $oldCartItem) {
                    $oldCartItem = $oldCartItem->toArray();
                    $oldCartItem['associatedModel'] = Product::find($oldCartItem['id']);
                    if ($oldCartItem['associatedModel']) {
                        $cart->add($oldCartItem);
                    }
                }
                // clear cookie cart
                $oldCart->clear();
            }

            // update cart items
            foreach($cart->getContent() as $cartItem) {
                $id = $cartItem->id;
                $product = Product::find($id);
                if(!$product) {
                    $cart->remove($cartItem->id);
                    continue;
                }

                $cart->update($cartItem->id, [
                    'price' => $product->price,
                    'associatedModel' => $product,
                ]);

                $discount = $product->price - $product->current_price;
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

            return $cart;
        });
    }
}
