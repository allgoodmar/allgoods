<?php

namespace App\Http\Controllers;

use App\Helpers\Breadcrumbs;
use App\Helpers\Helper;
use App\Helpers\LinkItem;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CompareController extends Controller
{
    public function index()
    {
        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->addItem(new LinkItem(__('main.compare_list'), route('compare.index'), LinkItem::STATUS_INACTIVE));

        $compare = app('compare');
        $compareItems = $compare->getContent()->sortBy('id');

        $locale = app()->getLocale();

        $compareList = [];
        $allAttributes = [];
        foreach($compareItems as $compareItem) {
            $product = $compareItem->associatedModel;
            $product->load('attributes', 'attributeValues', 'translations');
            $attributes = $product->attributes->translate();
            // $attributeValues = $product->attributes->translate();

            foreach($attributes as $attribute) {
                $allAttributes[$attribute->id] = $attribute->name;
            }

            $compareList[$compareItem->id] = [
                'product' => Helper::translation($product),
            ];
        }

        foreach ($compareList as $key => $value) {
            $product = $value['product'];
            $value['attributes'] = [];
            foreach($allAttributes as $attributeId => $attributeName) {
                $value['attributes'][$attributeId] = [];
                $productAttributeValues = $product->attributeValues->sortBy('name', SORT_NATURAL)->translate();
                foreach($productAttributeValues as $productAttributeValue) {
                    if ($productAttributeValue->attribute_id == $attributeId) {
                        $value['attributes'][$attributeId][] = $productAttributeValue->name;
                    }
                }
            }
            $compareList[$key] = $value;
        }

        return view('compare', compact('breadcrumbs', 'allAttributes', 'compareList', 'compareItems'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:products,id',
            'name' => 'required',
            'price' => 'required',
        ]);

        $data['quantity'] = 1;
        $data['associatedModel'] = Product::findOrFail($request->input('id'));

        // if (
        //     $data['associatedModel']->current_price != $data['price']
		// 	// || trim($data['associatedModel']->name) != trim($data['name'])
        // ) {
        //     abort(400);
        // }

        app('compare')->add($data);

        return response([
            'compare' => $this->getCompareInfo(app('compare')),
            'message' => __('main.product_added_to_compare'),
        ], 201);
    }

    public function delete($id)
    {
        app('compare')->remove($id);

        return response(array(
            'compare' => $this->getCompareInfo(app('compare')),
            'message' => __('main.product_removed_from_compare')
        ), 200);
    }

    private function getCompareInfo($compare)
    {
        $subtotal = $compare->getSubtotal();
        $total = $compare->getTotal();
        return [
            'quantity' => $compare->getTotalQuantity(),
            'subtotal' => $subtotal,
            'subtotalFormatted' => Helper::formatPrice($subtotal),
            'total' => $total,
            'totalFormatted' => Helper::formatPrice($total),
        ];
    }
}
