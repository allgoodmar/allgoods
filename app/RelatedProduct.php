<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatedProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id',
        'related_product_id'
    ];

    public function products()
    {
        return $this->belongsTo(Product::class);
    }
}
