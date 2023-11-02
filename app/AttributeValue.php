<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;

class AttributeValue extends Model
{
    use Resizable;
    use Translatable;
    use HasFactory;

    protected $translatable = ['name'];

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        self::saving(function($model){
            $model->name = str_replace([';', ':', '|'], ' ', $model->name);
        });
    }

    /**
     * Get attribute.
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Get products.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
