<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZoodpayRefund extends Model
{
    protected $guarded = [];

    public function zoodpayTransaction()
    {
        return $this->belongsTo(ZoodpayTransaction::class);
    }
}
