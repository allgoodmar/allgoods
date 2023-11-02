<?php

namespace App\Http\Resources;

use App\ProductGroup;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class ProductGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $appURL = config('app.url');
        $attributes = [];
        foreach ($this->attributes as $attribute) {
            $attributeType = $attribute->pivot->type;
            $row = [
                'id' => $attribute->id,
                'name' => $attribute->getTranslatedAttribute('name'),
                // 'type' => ProductGroup::attributeTypes()[$attributeType] ?? '',
                'type' => $attributeType,
                'attribute_values' => [],
            ];
            $attributeValues = $this->attributeValues->where('attribute_id', $attribute->id)->sortBy('name', SORT_NATURAL);
            foreach ($attributeValues as $attributeValue) {
                $row['attribute_values'][] = [
                    'id' => $attributeValue->id,
                    'name' => $attributeValue->getTranslatedAttribute('name'),
                    'image' => $attributeType == ProductGroup::ATTRIBUTE_TYPE_IMAGES ? $appURL . Storage::disk('public')->url($attributeValue->pivot->image) : '',
                ];
            }
            $attributes[] = $row;
        }

        $products = [];
        foreach ($this->products as $product) {
            $productAttributeValues = $product->attributeValues()->whereIn('attribute_id', $this->attributes->pluck('id'))->orderBy('id')->get();
            $products[] = [
                'attribute_value_ids' => $productAttributeValues->pluck('id')->toArray(),
                'product' => new ProductResource($product),
            ];
        }
        return [
            'id' => $this->id,
            'name' => $this->getTranslatedAttribute('name'),
            'attributes' => $attributes,
            // 'attributes' => AttributeResource::collection($this->attributes),
            'products' => $products,
            // 'products' => ProductResource::collection($this->products),
        ];
    }
}
