<div class="mb-4 product-group-items">
    @php
        // dd($productGroup->items());
        $productAttributeValuesIDs = $product->attributeValues->pluck('id');
    @endphp
    @foreach ($productGroup->attributes as $attribute)
        @php
            $attributeValues = $productGroup->attributeValues()->where('attribute_id', $attribute->id)->get();
        @endphp
        <div class="mb-3 product-group-attribute" data-attribute-id="{{ $attribute->id }}">
            <h5 class="mb-2">{{ $attribute->name }}</h5>
            <div>
                @foreach ($attributeValues->sortBy('name', SORT_NATURAL) as $attributeValue)
                    <span class="product-group-attribute-value" data-attribute-value-id="{{ $attributeValue->id }}">
                        <input type="radio" class="product-group-attribute-value-input"
                            name="product_group_attribute_{{ $attribute->id }}"
                            id="product_group_attribute_{{ $attribute->id }}_{{ $attributeValue->id }}"
                            value="{{ $attributeValue->id }}"
                            @if($productAttributeValuesIDs->contains($attributeValue->id)) checked @endif
                        >
                        <label class="d-inline-block py-1 px-2 small rounded product-group-attribute-value-label" for="product_group_attribute_{{ $attribute->id }}_{{ $attributeValue->id }}">
                            @if ($attribute->pivot->type == \App\ProductGroup::ATTRIBUTE_TYPE_IMAGES)
                                <img src="{{ Voyager::image($attributeValue->pivot->image) }}" alt="{{ $attributeValue->name }}" width="50" height="50" class="product-group-option-img" title="{{ $attributeValue->name }}">
                            @else
                                <span>{{ $attributeValue->name }}</span>
                            @endif
                        </label>
                    </span>
                @endforeach
            </div>
        </div>
    @endforeach

    @foreach ($productGroup->items() as $item)
        <input class="product-group-item" type="hidden" name="product_group_item[]" data-product-id="{{ $item['product']->id }}" data-combination="{{ $item['combination'] }}" data-url="{{ $item['product']->url . '?json=1' }}" data-in-stock="{{ $item['product']->in_stock }}" >
    @endforeach
</div>
