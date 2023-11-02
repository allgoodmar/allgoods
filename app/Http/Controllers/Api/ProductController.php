<?php

namespace App\Http\Controllers\Api;

use App\Brand;
use App\Category;
use App\Helpers\Helper;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends ApiController
{
    protected $select;
    protected $locales;

    public function __construct()
    {
        $this->select = ['id', 'name', 'description', 'slug', 'price', 'sale_price', 'installment_price', 'image', 'external_id', 'sku'];
        $this->locales = array_keys(config('laravellocalization.supportedLocales'));
    }

    public function all(Request $request)
    {
        $query = Product::select($this->select)->active();
        $categoryID = $request->input('category_id', '');
        $page = (int)$request->input('page', 1);
        if ($page < 1) {
            $page = 1;
        }
        $quantity = $request->input('quantity', 20);
        if ($quantity > 100) {
            $quantity = 100;
        }

        $offset = ($page - 1) * $quantity;

        if ($categoryID) {
            $query->whereHas('categories', function($q) use ($categoryID) {
                $q->where('categories.id', $categoryID);
            });
        }
        $query->with(['categories' => function($q){
            $q->select(['categories.id']);
        }]);

        $products = $query->skip($offset)->take($quantity)->withTranslations($this->locales)->get();
        foreach ($products as $key => $product) {
            $products[$key] = $this->formatProduct($product);
        }
        return response()->json($products->toArray());
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Product::select($this->select)->active();
        $categoryID = $request->input('category_id', '');
        $page = (int)$request->input('page', 1);
        if ($page < 1) {
            $page = 1;
        }
        $quantity = $request->input('quantity', 20);
        if ($quantity > 100) {
            $quantity = 100;
        }

        $offset = ($page - 1) * $quantity;

        if ($categoryID) {
            $query->whereHas('categories', function($q) use ($categoryID) {
                $q->where('categories.id', $categoryID);
            });
        }
        $query->with(['categories' => function($q){
            $q->select(['categories.id']);
        }]);

        // auth
        $query->where('user_id', $user->id);

        $products = $query->skip($offset)->take($quantity)->withTranslations($this->locales)->get();
        foreach ($products as $key => $product) {
            $products[$key] = $this->formatProduct($product);
        }
        return response()->json($products->toArray());
    }

    public function show(Request $request, Product $product)
    {
        // $user = auth()->user();
        // if ($product->user_id != $user->id) {
        //     return $this->message(403, 'Forbidden');
        // }

        $product = Product::select($this->select)
            ->withTranslations($this->locales)
            ->where('products.id', $product->id)
            ->with(['categories' => function($q){
                $q->select(['categories.id']);
            }])
            ->first();

        $product = $this->formatProduct($product);

        return response()->json($product);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'products' => 'required|array'
        ]);

        $user = auth()->user();

        $successData = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'rows' => [],
        ];

        $brands = Brand::pluck('id');
        $categories = Category::pluck('id');

        // process products
        foreach($request->products as $key => $uploadedProduct) {
            $row = 'Row ' . $key;
            $rowInfo = '';
            $rowErrors = [];
            if (empty($uploadedProduct['code']) || empty($uploadedProduct['name']) || empty($uploadedProduct['price'])) {
                if (empty($uploadedProduct['code'])) {
                    $rowErrors[] = 'Code is empty';
                }
                if (empty($uploadedProduct['name'])) {
                    $rowErrors[] = 'Name is empty';
                }
                if (empty($uploadedProduct['price'])) {
                    $rowErrors[] = 'Price is empty';
                }
                $successData['skipped']++;
                $rowInfo = 'Skipped. ' . implode('. ', $rowErrors);
                $successData['rows'][$row] = $rowInfo;
                continue;
            }

            $code = Str::limit($uploadedProduct['code'], 191, '');
            $name = !empty($uploadedProduct['name']) ? Str::limit($uploadedProduct['name'], 191, '') : '';
            $price = (float)$uploadedProduct['price'];
            $sku = !empty($uploadedProduct['sku']) ? Str::limit($uploadedProduct['sku'], 191, '') : Str::uuid();
            $installment_price = !empty($uploadedProduct['installment_price']) ? (float)$uploadedProduct['installment_price'] : $price;
            $sale_price = !empty($uploadedProduct['sale_price']) ? (float)$uploadedProduct['sale_price'] : 0;
            if ($sale_price >= $price) {
                $sale_price = 0;
            }
            $quantity = isset($uploadedProduct['quantity']) ? (int)$uploadedProduct['quantity'] : 0;
            $description = !empty($uploadedProduct['description']) ? Str::limit($uploadedProduct['description'], 50000, '') : '';
            $images = (isset($uploadedProduct['images']) && is_array($uploadedProduct['images'])) ? $uploadedProduct['images'] : [];
            $brand_id = isset($uploadedProduct['brand_id']) ? (int)$uploadedProduct['brand_id'] : null;
            $category_ids = isset($uploadedProduct['category_ids']) ? explode('|', $uploadedProduct['category_ids']) : [];

            $status = ($quantity > 0) ? Product::STATUS_ACTIVE : Product::STATUS_INACTIVE;

            // get product
            $product = Product::where('user_id', $user->id)->where('external_id', $code)->first();
            if ($product) {
                // update product
                $updateData = [
                    'name' => $name,
                    'sku' => $sku,
                    'in_stock' => $quantity,
                    'price' => $price,
                    'installment_price' => $installment_price,
                    'sale_price' => $sale_price,
                    'body' => $description,
                    'description' => Str::limit(strip_tags($description), 150, '...'),
                    'status' => $status,
                ];
                if ($brands->contains($brand_id)) {
                    $updateData['brand_id'] = $brand_id;
                }
                $product->update($updateData);
                $rowInfo = 'Updated';
                $successData['rows'][$row] = $rowInfo;
                $successData['updated']++;
            } else {
                // create new product
                $createData = [
                    'source' => Product::SOURCE_API,
                    'external_id' => $code,
                    'user_id' => $user->id,
                    'name' => $name,
                    'sku' => $sku,
                    'slug' => Str::slug($name),
                    'in_stock' => $quantity,
                    'price' => $price,
                    'installment_price' => $installment_price,
                    'sale_price' => $sale_price,
                    'body' => $description,
                    'description' => Str::limit(strip_tags($description), 150, '...'),
                    'status' => $status,
                ];
                if ($brands->contains($brand_id)) {
                    $createData['brand_id'] = $brand_id;
                }
                $product = Product::create($createData);
                $rowInfo = 'Created';
                $successData['rows'][$row] = $rowInfo;
                $successData['created']++;

                if (count($images)) {
                    $imageURL = array_shift($images);
                    Helper::storeImageFromUrl($imageURL, $product, 'image', 'products', Product::$imgSizes);
                    if (count($images)) {
                        $images = array_slice($images, 0, 6);
                        Helper::storeImagesFromUrl($images, $product, 'images', 'products', Product::$imgSizes);
                    }
                }
            }
            if (count($category_ids)) {
                $syncCategoryIDs = [];
                foreach ($category_ids as $category_id) {
                    if ($categories->contains($category_id)) {
                        $syncCategoryIDs[] = $category_id;
                    }
                }
                if (count($syncCategoryIDs)) {
                    $product->categories()->sync($syncCategoryIDs);
                }
            }

        }

        return $this->message(200, 'Products uploaded', $successData);
    }

    private function formatProduct($product)
    {
        $appURL = config('app.url');
        $translations = [];
        foreach ($this->locales as $locale) {
            $translations[$locale] = [];
            foreach ($this->select as $selectValue) {
                $translation = $product['translations']->where('locale', $locale)->where('column_name', $selectValue)->first();
                if ($translation) {
                    $translations[$locale][$selectValue] = $translation->value;
                }
            }
            if (empty($translations[$locale])) {
                unset($translations[$locale]);
            }
        }
        $product['languages'] = $translations;

        $product['price'] = $product->current_price;
        $product['code'] = $product->external_id;
        $product = $product->toArray();
        $product['img'] = $appURL . $product['img'];

        unset($product['translations']);
        unset($product['categories']);
        unset($product['image']);
        unset($product['price']);
        unset($product['sale_price']);
        unset($product['external_id']);

        return $product;
    }
}
