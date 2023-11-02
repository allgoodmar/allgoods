<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class ProductGroup extends Model
{
    use Translatable;
    use HasFactory;

    const ATTRIBUTE_TYPE_BUTTONS = 0;
    const ATTRIBUTE_TYPE_COLORS = 1;
    const ATTRIBUTE_TYPE_IMAGES = 2;

    protected $guarded = [];

    protected $translatable = ['name'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class)->withPivot('type');
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class)->withPivot(['image', 'color', 'name']);
    }

    public function items()
    {
        $items = [];
        foreach ($this->products as $product) {
            if (!$product->isAvailable()) {
                continue;
            }
            $productAttributeValues = $product->attributeValues()->whereIn('attribute_id', $this->attributes()->pluck('attributes.id'))->orderBy('id')->get();
            $items[$product->id] = [
                'product' => $product,
                'combination' => implode('-', $productAttributeValues->pluck('id')->toArray()),
            ];
        }
        return collect($items);
    }

    public static function attributeTypes()
    {
        return [
            static::ATTRIBUTE_TYPE_BUTTONS => 'Кнопки',
            // static::ATTRIBUTE_TYPE_COLORS => 'Цвет',
            static::ATTRIBUTE_TYPE_IMAGES => 'Картинки',
        ];
    }

    public function syncAttributeValues()
    {
        $attributeValueIDs = collect();
        $attributeIDs = $this->attributes()->pluck('attributes.id');
        $this->load('products');
        foreach ($this->products as $groupProduct) {
            $groupProductAttributeValueIDs = $groupProduct->attributeValues()->whereIn('attribute_id', $attributeIDs)->pluck('attribute_values.id');
            $attributeValueIDs = $attributeValueIDs->merge($groupProductAttributeValueIDs);
        }
        $this->attributeValues()->sync($attributeValueIDs);
    }
}
