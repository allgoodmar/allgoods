<?php

namespace App\Storages;

use App\Wishlist;
use Darryldecode\Cart\CartCollection;
use Illuminate\Support\Facades\Cache;

class WishlistStorage
{
    public function has($key)
    {
        return $this->getWishlist($key);
    }

    public function get($key)
    {
        if ($this->has($key)) {
            return new CartCollection($this->getWishlist($key)->cart_data);
        } else {
            return [];
        }
    }

    public function put($key, $value)
    {
        if ($row = Wishlist::find($key)) {
            Cache::forget($key);
            // update
            $row->cart_data = $value;
            $row->save();
        } else {
            Wishlist::create([
                'id'        => $key,
                'cart_data' => $value
            ]);
            Cache::forget($key);
        }
    }

    public function getWishlist($key)
    {
        return Cache::remember($key . '_' . app()->getLocale(), 3600, function () use ($key) {
            return Wishlist::find($key);
        });
    }
}
