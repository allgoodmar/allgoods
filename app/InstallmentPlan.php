<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstallmentPlan extends Model
{
    protected $guarded = [];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'installment_plan_product');
    }
}
