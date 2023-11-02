<?php

namespace App\Providers;

use App\Product;
use App\Storages\CompareStorage;
use Darryldecode\Cart\Cart;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class CompareServiceProvider extends ServiceProvider
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
        $this->app->singleton('compare', function($app)
        {
            $storage = new CompareStorage();
            $events = $app['events'];
            $instanceName = 'compare';

            if (auth()->check()) {
                // user compare
                $sessionKey = auth()->user()->id;
            } else {
                // cookie compare
                $sessionKey = request()->cookie('compare_session_key', Str::random(30));
                if (Str::length($sessionKey) > 30) {
                    $sessionKey = Str::substr($sessionKey, 0, 30);
                }
                if (!Cookie::has('compare_session_key')) {
                    Cookie::queue('compare_session_key', $sessionKey, 1440 * 30);
                }
            }

            $config = config('shopping_cart');

            // new compare
            $compare = new Cart(
                $storage,
                $events,
                $instanceName,
                $sessionKey,
                $config
            );

            // if user logged in check cookie compare
            $cookieSessionKey = Cookie::get('compare_session_key');
            if (Str::length($cookieSessionKey) > 30) {
                $cookieSessionKey = Str::substr($cookieSessionKey, 0, 30);
            }
            if (auth()->check() && $cookieSessionKey) {
                $oldCompare = new Cart(
                    $storage,
                    $events,
                    $instanceName,
                    $cookieSessionKey,
                    $config
                );
                // add cookie compare items to user compare
                foreach($oldCompare->getContent() as $oldCompareItem) {
                    $oldCompareItem = $oldCompareItem->toArray();
                    $oldCompareItem['associatedModel'] = Product::find($oldCompareItem['id']);
                    if ($oldCompareItem['associatedModel']) {
                        $compare->add($oldCompareItem);
                    }
                }
                // clear cookie compare
                $oldCompare->clear();
            }

            // update compare items
            foreach($compare->getContent() as $compareItem) {
                $product = Product::find($compareItem->id);
                if(!$product) {
                    $compare->remove($compareItem->id);
                    continue;
                }
                $compare->update($compareItem->id, [
                    'price' => $product->current_price,
                    'associatedModel' => $product,
                ]);
            }

            return $compare;
        });
    }
}
