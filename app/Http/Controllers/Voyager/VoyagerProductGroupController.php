<?php

namespace App\Http\Controllers\Voyager;

use App\AttributeValueProductGroup;
use App\Product;
use App\ProductGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use TCG\Voyager\Facades\Voyager;
use Intervention\Image\Facades\Image;

class VoyagerProductGroupController extends VoyagerBaseController
{
    public function settings(Request $request, ProductGroup $productGroup)
    {
        // Check permission
        $this->authorize('edit', $productGroup);

        $productGroup->load('attributes');
        $attributeIDs = $productGroup->attributes->pluck('id');

        $productGroup->load(['products' => function ($q1) use ($attributeIDs) {
            $q1
                ->with(['attributes' => function($q2) use ($attributeIDs) {
                    $q2->whereIn('attributes.id', $attributeIDs)->with(['attributeValues']);
                }])
                ->with(['attributeValues']);
        }]);

        return Voyager::view('voyager::product_groups.settings', compact('productGroup'));
    }

    public function productsIndex (ProductGroup $productGroup)
    {
        return redirect()->route('voyager.product_groups.settings', [$productGroup->id]);
    }

    public function productsCreate (ProductGroup $productGroup)
    {
        // Check permission
        $this->authorize('edit', $productGroup);
        $productGroup->load('attributes');
        return Voyager::view('voyager::product_groups.products.create', compact('productGroup'));
    }

    public function productsStore(Request $request, ProductGroup $productGroup)
    {
        // Check permission
        $this->authorize('edit', $productGroup);

        // validate
        $request->validate([
            'id' => 'required|exists:products,id',
        ]);

        $attributeIDs = $productGroup->attributes->pluck('id');
        $productGroupProductsIDs = $productGroup->products->pluck('id');

        // get product with attributes and values
        $product = Product::where('id', $request->id)->with(['attributeValues' => function($q) use ($attributeIDs) {
            $q->with('attribute')->whereHas('attribute', function($q1) use ($attributeIDs) {
                $q1->whereIn('id', $attributeIDs);
            });
        }])->first();

        // check already in group
        if ($productGroupProductsIDs->contains($product->id)) {
            return back()->withErrors([
                'attributes' => 'Продукт уже добавлен в группу',
            ])->withInput();
        }

        // validate only one value selected in product attribute
        $selectedProductAttributeIDs = [];
        $selectedProductAttributeValueIDs = [];
        foreach ($product->attributeValues as $attributeValue) {
            if (in_array($attributeValue->attribute->id, $selectedProductAttributeIDs)) {
                return back()->withErrors([
                    'attributes' => 'У продукта к аттрибуту ' . $attributeValue->attribute->name . ' должно быть привязано только одно значение',
                ])->withInput();
            }
            $selectedProductAttributeIDs[] = $attributeValue->attribute->id;
            $selectedProductAttributeValueIDs[] = $attributeValue->id;
        }

        // validate all attributes selected
        if (count($selectedProductAttributeIDs) != $attributeIDs->count()) {
            return back()->withErrors([
                'attributes' => 'Добавьте к продукту все требуемые значения атрибутов',
            ])->withInput();
        }

        // check there is no product with same attribute values
        foreach ($productGroup->products as $groupProduct) {
            $groupProduct->load('attributeValues');
            $groupProductAttributeValueIDs = $groupProduct->attributeValues->pluck('id');
            $productIsUniqueInGroup = false;
            foreach($selectedProductAttributeValueIDs as $value) {
                if (!$groupProductAttributeValueIDs->contains($value)) {
                    $productIsUniqueInGroup = true;
                }
            }
            if (!$productIsUniqueInGroup) {
                $errorMessage = 'Продукт с этими значениями атрибутов уже присутствует в группе';
                $productAttributesInfo = [];
                foreach ($product->attributeValues as $attributeValue) {
                    $productAttributesInfo[] = $attributeValue->attribute->name . ': ' . $attributeValue->name;
                }
                $errorMessage .= ' (' . implode(' | ', $productAttributesInfo) . ')';
                return back()->withErrors([
                    'attributes' => $errorMessage,
                ])->withInput();
            }
        }

        // save
        $productGroup->products()->save($product);
        $productGroup->attributeValues()->syncWithoutDetaching($selectedProductAttributeValueIDs);

        return redirect()->route('voyager.product_groups.settings', [$productGroup->id])->with([
            'message'    => 'Продукт добавлен в группу',
            'alert-type' => 'success',
        ]);
    }

    public function productsDetach(ProductGroup $productGroup, Product $product)
    {
        // Check permission
        $this->authorize('edit', $productGroup);
        $product->productGroup()->dissociate();
        $product->saveQuietly();

        $productGroup->syncAttributeValues();

        return redirect()->route('voyager.product_groups.settings', [$productGroup->id])->with([
            'message'    => 'Продукт удален из группы',
            'alert-type' => 'success',
        ]);
    }

    public function attributesUpdate(Request $request, ProductGroup $productGroup)
    {
        $data = $request->validate([
            'attributes' => 'required|array',
            'attributes.*.type' => 'required|in:' . implode(',', array_keys(ProductGroup::attributeTypes())),
        ]);
        $sync = [];
        foreach ($data['attributes'] as $key => $value) {
            $sync[$key] = [
                'type' => $value['type'],
            ];
        }
        $productGroup->attributes()->sync($sync);

        return redirect()->back()->with([
            'message'    => 'Настройки сохранены',
            'alert-type' => 'success',
        ]);
    }

    public function attributeValuesUpdate(Request $request, ProductGroup $productGroup)
    {
        $data = $request->validate([
            'attribute_values' => 'required|array',
            'attribute_values.*.image' => 'nullable|image|max:512',
        ]);
        $sync = [];
        foreach ($data['attribute_values'] as $key => $value) {
            if (!empty($value['image'])) {
                $attributeValueProductGroup = AttributeValueProductGroup::where('attribute_value_id', $key)->where('product_group_id', $productGroup->id)->firstOrFail();
                if ($attributeValueProductGroup->image) {
                    Storage::disk('public')->delete($attributeValueProductGroup->image);
                }
                $dir = 'attribute-value-product-group/' . $productGroup->id . '/' . $key;
                $path = $value['image']->store($dir, 'public');
                $image = Image::make(Storage::disk('public')->path($path));
                if ($image) {
                    $image->fit(Product::$imgSizes['medium'][0], Product::$imgSizes['medium'][1])->save();
                }
                $attributeValueProductGroup->update([
                    'image' => $path,
                ]);
            }
        }

        return redirect()->back()->with([
            'message'    => 'Настройки сохранены',
            'alert-type' => 'success',
        ]);
    }

}
