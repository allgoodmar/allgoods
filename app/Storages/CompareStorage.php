<?php

namespace App\Storages;

use App\Compare;
use Darryldecode\Cart\CartCollection;

class CompareStorage
{
    public function has($key)
    {
        return Compare::find($key);
    }

    public function get($key)
    {
        if ($this->has($key)) {
            return new CartCollection(Compare::find($key)->cart_data);
        } else {
            return [];
        }
    }

    public function put($key, $value)
    {
        if ($row = Compare::find($key)) {
            // update
            $row->cart_data = $value;
            $row->save();
        } else {
            Compare::create([
                'id' => $key,
                'cart_data' => $value
            ]);
        }
    }
}
