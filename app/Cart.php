<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Throwable;

class Cart extends Model
{
    protected $fillable = [
        'id', 'cart_data',
    ];

    public function setCartDataAttribute($value)
    {
        $this->attributes['cart_data'] = serialize($value);
    }

    public function getCartDataAttribute($value)
    {
        try {
            $value = unserialize($value);
            return $value;
        } catch (Throwable $e) {
            //
            $this->attributes['cart_data'] = serialize([]);
            return [];
        }
    }
}
