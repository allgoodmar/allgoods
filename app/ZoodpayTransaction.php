<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZoodpayTransaction extends Model
{
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function zoodpayRefunds()
    {
        return $this->hasMany(ZoodpayRefund::class);
    }
}
