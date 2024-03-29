<?php

namespace App\Providers;

use App\Product;
use App\Storages\WishlistStorage;
use Darryldecode\Cart\Cart;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;


class WishlistServiceProvider extends ServiceProvider
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

        $this->app->singleton('wishlist', function($app)
        {
            $key = request()->cookie('wishlist_session_key', Str::random(30));
            Cache::forget($key.'_cart_items'. '_' . app()->getLocale());
            $storage = new WishlistStorage();
            $events = $app['events'];
            $instanceName = 'wishlist';

            if (auth()->check()) {
                // user wishlist
                $sessionKey = auth()->user()->id;
            } else {
                // cookie wishlist
                $sessionKey = request()->cookie('wishlist_session_key', Str::random(30));
                if (Str::length($sessionKey) > 30) {
                    $sessionKey = Str::substr($sessionKey, 0, 30);
                }
                if (!Cookie::has('wishlist_session_key')) {
                    Cookie::queue('wishlist_session_key', $sessionKey, 1440 * 30);
                }
            }

            $config = config('shopping_cart');

            // new wishlist
            $wishlist = new Cart(
                $storage,
                $events,
                $instanceName,
                $sessionKey,
                $config
            );

            // if user logged in check cookie wishlist
            $cookieSessionKey = Cookie::get('wishlist_session_key');
            if (Str::length($cookieSessionKey) > 30) {
                $cookieSessionKey = Str::substr($cookieSessionKey, 0, 30);
            }
            if (auth()->check() && $cookieSessionKey) {
                $oldWishlist = new Cart(
                    $storage,
                    $events,
                    $instanceName,
                    $cookieSessionKey,
                    $config
                );
                // add cookie wishlist items to user wishlist
                foreach($oldWishlist->getContent() as $oldWishlistItem) {
                    $oldWishlistItem = $oldWishlistItem->toArray();
                    $oldWishlistItem['associatedModel'] = Product::find($oldWishlistItem['id']);
                    if ($oldWishlistItem['associatedModel']) {
                        $wishlist->add($oldWishlistItem);
                    }
                }
                // clear cookie wishlist
                $oldWishlist->clear();
            }

            // update wishlist items
            foreach($wishlist->getContent() as $wishlistItem) {
                $product = Product::query()->find($wishlistItem->id);
                if(!$product) {
                    $wishlist->remove($wishlistItem->id);
                    continue;
                }
                $wishlist->update($wishlistItem->id, [
                    'price' => $product->current_price,
                    'associatedModel' => $product,
                ]);
            }
            return $wishlist;
        });
    }
}
