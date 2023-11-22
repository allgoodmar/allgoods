<?php

namespace App\Storages;

use App\Cart;
use App\Wishlist;
use Darryldecode\Cart\CartCollection;
use Illuminate\Support\Facades\Cache;

class CartStorage
{
    public function has($key)
    {
        return $this->getCartlist($key);
    }

    public function get($key)
    {
        if ($this->has($key)) {
            return new CartCollection($this->getCartlist($key)->cart_data);
        } else {
            return [];
        }
    }

    public function put($key, $value)
    {
        if ($row = Cart::find($key)) {
            Cache::forget($key);
            // update
            $row->cart_data = $value;
            $row->save();
        } else {
            Cart::create([
                'id' => $key,
                'cart_data' => $value
            ]);
            Cache::forget($key);
        }
    }

    public function getCartList($key)
    {
        return Cache::remember($key . '_' . app()->getLocale(), 3600, function () use ($key) {
            return Cart::find($key);
        });
    }
}
