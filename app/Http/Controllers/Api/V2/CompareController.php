<?php

namespace App\Http\Controllers\Api\V2;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index(Request $request)
    {
        $compareData = $this->getData();
        return response()->json($compareData);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'products' => 'required|array|max:5',
            'products.*.id' => 'required|exists:products,id',
        ]);

        $locale = app()->getLocale();
        $compare = app('compare');

        $ids = collect($data['products'])->pluck('id');
        $products = Product::withTranslation($locale)->with('brand')->whereIn('id', $ids)->get();

        // clear compare
        $compare->clear();

        // add items to compare
        foreach ($data['products'] as $value) {
            $product = $products->where('id', $value['id'])->first();
            $compare->add([
                'id' => $product->id,
                'name' => $product->getTranslatedAttribute('name'),
                'price' => $product->current_not_sale_price,
                'quantity' => 1,
                'associatedModel' => $product,
            ]);
        }

        $compareData = $this->getData();
        return response()->json($compareData);
    }

    public function clear()
    {
        $compare = app('compare');
        $compare->clear();

        return response()->json([
            'message' => __('main.compare_list_has_been_cleared'),
        ]);
    }

    public function itemsAdd(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $locale = app()->getLocale();
        $compare = app('compare');

        if ($compare->getTotalQuantity() >= 5) {
            return response()->json([
                'message' => __('The given data was invalid.'),
                'errors' => [
                    'product_id' => [
                        __('validation.max.array', ['max' => 5]),
                    ],
                ],
            ], 422);
        }

        if (!$compare->get($data['product_id'])) {
            // get product
            $product = Product::withTranslation($locale)->where('id', $data['product_id'])->first();

            // add item
            $compare->add([
                'id' => $product->id,
                'name' => $product->getTranslatedAttribute('name'),
                'price' => $product->current_price,
                'quantity' => 1,
                'associatedModel' => $product,
            ]);
        }

        return response()->json([
            'message' => __('main.product_added_to_compare'),
        ]);
    }

    public function itemsRemove(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required',
        ]);

        $compare = app('compare');
        $compare->remove($data['product_id']);

        return response()->json([
            'message' => __('main.product_removed_from_compare'),
        ]);
    }

    public function itemsRemoveMultiple(Request $request)
    {
        $data = $request->validate([
            'product_ids' => 'required|array',
        ]);

        $compare = app('compare');
        foreach ($data['product_ids'] as $id) {
            $compare->remove($id);
        }

        return response()->json([
            'message' => __('main.product_removed_from_compare'),
        ]);
    }

    private function getData()
    {
        $compare = app('compare');
        $items = $compare->getContent()->sortBy('id');
        $data = [
            'quantity' => $compare->getTotalQuantity(),
            'items' => [],
        ];
        foreach ($items as $item) {
            $data['items'][] = [
                'id' => $item->id,
                'name' => $item->name,
                'product' => new ProductResource($item->associatedModel),
            ];
        }
        return $data;
    }
}
