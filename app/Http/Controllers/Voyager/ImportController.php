<?php

namespace App\Http\Controllers\Voyager;

use App\Attribute;
use App\AttributeValue;
use App\Brand;
use App\Category;
use App\Company;
use App\InstallmentPlan;
use App\Product;
use App\Rubric;
use App\Services\Smartup;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;

class ImportController extends VoyagerBaseController
{
    private $uploadedProductsFile = 'last-uploaded-products.xlsx';

    public function index(Request $request)
    {
        $this->checkPermissions();
        $test = '';
        return Voyager::view('voyager::import.index', compact('test'));
    }

    public function products(Request $request)
    {
        $this->checkPermissions();

        $request->validate([
            'products' => 'required|mimes:xlsx',
        ]);

        $brands = Brand::all()->keyBy('name');
        // $categories = Category::all()->keyBy('name');
        $categories = Category::all()->keyBy('id');

        $attributes = Attribute::with('attributeValues')->get()->keyBy('name');
        // $attribute->attribute_values_key_by_name

        $request->file('products')->storeAs('import', $this->uploadedProductsFile);
        $filePath = Storage::path('import/' . $this->uploadedProductsFile);
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($filePath);
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $key => $row) {
                // ignore title row
                if ($key == 1 || $key == 2) {
                    continue;
                }

                // upload product
                $cells = $row->getCells();

                $id = isset($cells[0]) ? (int)trim($cells[0]->getValue()) : '';
                $name = isset($cells[4]) ? trim($cells[4]->getValue()) : '';
                $sku = isset($cells[1]) ? (string)trim($cells[1]->getValue()) : '';
                $brandName = isset($cells[3]) ? trim($cells[3]->getValue()) : '';
                $categoryIDs = isset($cells[2]) ? trim($cells[2]->getValue()) : '';
                $installment_price = isset($cells[8]) ? (string)trim($cells[8]->getValue()) : 0;
                $price = isset($cells[5]) ? (string)trim($cells[5]->getValue()) : 0;
                $sale_price = isset($cells[7]) ? (string)trim($cells[7]->getValue()) : 0;
                $in_stock = isset($cells[9]) ? (int)trim($cells[9]->getValue()) : 0;
                // $optionAttributes = isset($cells[6]) ? trim($cells[6]->getValue()) : '';
                $optionAttributes = '';
                $otherAttributes = isset($cells[10]) ? trim($cells[10]->getValue()) : '';
                $description = isset($cells[11]) ? trim($cells[11]->getValue()) : '';
				$status = isset($cells[13]) ? (int)trim($cells[13]->getValue()) : Product::STATUS_PENDING;
                $rating = isset($cells[14]) ? (float)trim($cells[14]->getValue()) : 0;
                $reviews = isset($cells[15]) ? trim($cells[15]->getValue()) : '';
                $numberOfSales = isset($cells[16]) ? (int)trim($cells[16]->getValue()) : 0;
                $order = isset($cells[17]) ? (int)trim($cells[17]->getValue()) : 990;
                $isHidden = isset($cells[18]) ? (int)trim($cells[18]->getValue()) : 0;
                $youtubelink = isset($cells[19]) ? (int)trim($cells[19]->getValue()) : null;
                $sale_end_date = isset($cells[20]) ? (int)trim($cells[20]->getValue()) : null;


                // ignore if name empty
                if (!$name) {
                    continue;
                }

                $name = Str::limit($name, 191, '');
                $sku = Str::limit($sku, 191, '');
                $brandName = Str::limit($brandName, 191, '');
                $description = Str::limit($description, 65000, '');

                $installment_price = (float)str_replace([' ', ','], ['', '.'], $installment_price);
                $price = (float)str_replace([' ', ','], ['', '.'], $price);
                $sale_price = (float)str_replace([' ', ','], ['', '.'], $sale_price);

                if (!array_key_exists($status, Product::statuses())) {
                    $status = Product::STATUS_PENDING;
                }

                if (!isset($brands[$brandName])) {
                    $brands[$brandName] = Brand::create(['name' => $brandName, 'slug' => Str::slug($brandName), 'status' => Brand::STATUS_ACTIVE]);
                }
                $brand_id =  $brands[$brandName]->id;

                $syncCategoryIDs = [];
                if ($categoryIDs) {
                    $categoryIDs = explode('|', $categoryIDs);
                    $categoryIDs = array_map(function($item){
                        return (int)trim($item);
                    }, $categoryIDs);
                }
                if (is_array($categoryIDs)) {
                    foreach ($categoryIDs as $value) {
                        if (isset($categories[$value])) {
                            $syncCategoryIDs[] = $value;
                        }
                    }
                }

                // set data
                // $findData = [
                //     'id' => $id,
                // ];
                $createData = [
                    'slug' => Str::slug($name),
                    'source' => Product::SOURCE_IMPORT,
                ];
                $updateData = [
                    'sku' => $sku,
                    'brand_id' => $brand_id,
                    'name' => $name,
                    'installment_price' => $installment_price,
                    'price' => $price,
                    'sale_price' => $sale_price,
                    'in_stock' => $in_stock,
                    'status' => $status,
                    'number_of_sales' => $numberOfSales,
                    'order' => $order,
                    // 'description' => $description,
                    'body' => $description,
                    'is_hidden' => $isHidden,
                    'youtubelink' => $youtubelink,
                    'sale_end_date' => $sale_end_date
                ];

                $product = null;
                if ($id) {
                    $product = Product::find($id);
                }
                // $query = Product::query();
                // foreach($findData as $key => $value) {
                //     $query->where($key, $value);
                // }
                // $product = $query->first();

                // create if no product
                if (!$product) {
                    // add new product
                    // $createData = array_merge($createData, $updateData, $findData);
                    $createData = array_merge($createData, $updateData);
                    $createData['status'] = $updateData['status'] = Product::STATUS_PENDING;
                    $product = Product::create($createData);
                }

                // categories
                // Log::info($syncCategoryIDs);
                $product->categories()->sync($syncCategoryIDs);

                // attributes
                $productOtherAttributes = [];
                $productOtherAttributeIDs = [];
                $productOtherAttributeValues = [];
                $productOtherAttributeValueIDs = [];

                // simple product
                $product->update($updateData);

                // update attributes
                // sanitize attributes
                $otherAttributesArray = [];
                $otherAttributesRaw = explode('|', $otherAttributes);
                foreach($otherAttributesRaw as $value) {
                    $value = explode(':', $value);
                    if (empty($value[0]) || empty($value[1])) {
                        // some error in attributes - ignore row
                        continue;
                    }
                    $value0 = Str::limit(trim(str_replace([';', ':', '|'], ' ', $value[0])), 191, '');
                    $value1 = Str::limit(trim(str_replace([';', ':', '|'], ' ', $value[1])), 191, '');
                    $otherAttributesArray[$value0] = Str::limit($value1);
                }

                if (count($otherAttributesArray)) {
                    // otherAttributes sanitized - sync attributes
                    foreach ($otherAttributesArray as $key => $value) {

                        // get attribute
                        if (!isset($attributes[$key])) {
                            $attributes[$key] = Attribute::create(['name' => $key]);
                        }
                        $attribute = $attributes[$key];

                        // get attribute value
                        if (!isset($attribute->attribute_values_key_by_name[$value])) {
                            AttributeValue::create(['name' => $value, 'attribute_id' => $attribute->id]);
                            $attribute->refresh();
                        }
                        $attributeValue = $attribute->attribute_values_key_by_name[$value];

                        $productOtherAttributes[] = $attribute->name;
                        $productOtherAttributeIDs[] = (int)$attribute->id;
                        $productOtherAttributeValues[] = $attributeValue->name;
                        $productOtherAttributeValueIDs[] = (int)$attributeValue->id;
                    }
                }
                $productAllAttributes = [];
                $productAllAttributeValues = [];
                foreach($productOtherAttributeIDs as $productOtherAttributeID) {
                    $productAllAttributes[] = $productOtherAttributeID;
                }
                foreach($productOtherAttributeValueIDs as $productOtherAttributeValueID) {
                    $productAllAttributeValues[] = $productOtherAttributeValueID;
                }
                $product->attributes()->syncWithoutDetaching($productAllAttributes);
                $product->attributeValues()->syncWithoutDetaching($productAllAttributeValues);

            }
            // ignore other sheets
            break;
        }
        $reader->close();

        return redirect()->route('voyager.import.index')->with([
            'message'    => 'Products uploaded',
            'alert-type' => 'success',
        ]);
    }

    public function productsFromMerchant(Request $request, $company = null)
    {
        $request->validate([
            'products' => 'required|mimes:xlsx',
        ]);

        $brands = Brand::all()->keyBy('name');
        // $categories = Category::all()->keyBy('name');
        $categories = Category::all()->keyBy('id');

        $attributes = Attribute::with('attributeValues')->get()->keyBy('name');
        // $attribute->attribute_values_key_by_name

        $request->file('products')->storeAs('import', $this->uploadedProductsFile);
        $filePath = Storage::path('import/' . $this->uploadedProductsFile);
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($filePath);
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $key => $row) {
                // ignore title row
                if ($key == 1 || $key == 2) {
                    continue;
                }

                // upload product
                $cells = $row->getCells();

                $id = isset($cells[0]) ? (int)trim($cells[0]->getValue()) : '';
                $name = isset($cells[4]) ? trim($cells[4]->getValue()) : '';
                $sku = isset($cells[1]) ? (string)trim($cells[1]->getValue()) : '';
                $brandName = isset($cells[3]) ? trim($cells[3]->getValue()) : '';
                $categoryIDs = isset($cells[2]) ? trim($cells[2]->getValue()) : '';
                $installment_price = isset($cells[8]) ? (string)trim($cells[8]->getValue()) : 0;
                $price = isset($cells[5]) ? (string)trim($cells[5]->getValue()) : 0;
                $sale_price = isset($cells[7]) ? (string)trim($cells[7]->getValue()) : 0;
                $in_stock = isset($cells[9]) ? (int)trim($cells[9]->getValue()) : 0;
                // $optionAttributes = isset($cells[6]) ? trim($cells[6]->getValue()) : '';
                $optionAttributes = '';
                $otherAttributes = isset($cells[10]) ? trim($cells[10]->getValue()) : '';
                $description = isset($cells[11]) ? trim($cells[11]->getValue()) : '';
				$status = isset($cells[13]) ? (int)trim($cells[13]->getValue()) : Product::STATUS_PENDING;
                $rating = isset($cells[14]) ? (float)trim($cells[14]->getValue()) : 0;
                $reviews = isset($cells[15]) ? trim($cells[15]->getValue()) : '';
                $numberOfSales = isset($cells[16]) ? (int)trim($cells[16]->getValue()) : 0;
                $order = isset($cells[17]) ? (int)trim($cells[17]->getValue()) : 990;
                $isHidden = isset($cells[18]) ? (int)trim($cells[18]->getValue()) : 0;
                $youtubelink = isset($cells[19]) ? (int)trim($cells[19]->getValue()) : null;


                // ignore if name empty
                if (!$name) {
                    continue;
                }

                $name = Str::limit($name, 191, '');
                $sku = Str::limit($sku, 191, '');
                $brandName = Str::limit($brandName, 191, '');
                $description = Str::limit($description, 65000, '');

                $installment_price = (float)str_replace([' ', ','], ['', '.'], $installment_price);
                $price = (float)str_replace([' ', ','], ['', '.'], $price);
                $sale_price = (float)str_replace([' ', ','], ['', '.'], $sale_price);

                if (!array_key_exists($status, Product::statuses())) {
                    $status = Product::STATUS_PENDING;
                }

                if (!isset($brands[$brandName])) {
                    $brands[$brandName] = Brand::create(['name' => $brandName, 'slug' => Str::slug($brandName), 'status' => Brand::STATUS_ACTIVE]);
                }
                $brand_id =  $brands[$brandName]->id;

                $syncCategoryIDs = [];
                if ($categoryIDs) {
                    $categoryIDs = explode('|', $categoryIDs);
                    $categoryIDs = array_map(function($item){
                        return (int)trim($item);
                    }, $categoryIDs);
                }
                if (is_array($categoryIDs)) {
                    foreach ($categoryIDs as $value) {
                        if (isset($categories[$value])) {
                            $syncCategoryIDs[] = $value;
                        }
                    }
                }

                // set data
                // $findData = [
                //     'id' => $id,
                // ];
                $createData = [
                    'slug' => Str::slug($name),
                    'source' => Product::SOURCE_IMPORT,
                ];
                $updateData = [
                    'seller_id' => $company,
                    'sku' => $sku,
                    'brand_id' => $brand_id,
                    'name' => $name,
                    'installment_price' => $installment_price,
                    'price' => $price,
                    'sale_price' => $sale_price,
                    'in_stock' => $in_stock,
                    'status' => $status,
                    'number_of_sales' => $numberOfSales,
                    'order' => $order,
                    // 'description' => $description,
                    'body' => $description,
                    'is_hidden' => $isHidden,
                    'youtubelink' => $youtubelink,
                ];

                $product = null;
                if ($id) {
                    $product = Product::find($id);
                }
                // $query = Product::query();
                // foreach($findData as $key => $value) {
                //     $query->where($key, $value);
                // }
                // $product = $query->first();

                // create if no product
                if (!$product) {
                    // add new product
                    // $createData = array_merge($createData, $updateData, $findData);
                    $createData = array_merge($createData, $updateData);
                    $createData['status'] = $updateData['status'] = Product::STATUS_PENDING;
                    $product = Product::create($createData);
                }

                // categories
                // Log::info($syncCategoryIDs);
                $product->categories()->sync($syncCategoryIDs);

                // attributes
                $productOtherAttributes = [];
                $productOtherAttributeIDs = [];
                $productOtherAttributeValues = [];
                $productOtherAttributeValueIDs = [];

                // simple product
                $product->update($updateData);

                // update attributes
                // sanitize attributes
                $otherAttributesArray = [];
                $otherAttributesRaw = explode('|', $otherAttributes);
                foreach($otherAttributesRaw as $value) {
                    $value = explode(':', $value);
                    if (empty($value[0]) || empty($value[1])) {
                        // some error in attributes - ignore row
                        continue;
                    }
                    $value0 = Str::limit(trim(str_replace([';', ':', '|'], ' ', $value[0])), 191, '');
                    $value1 = Str::limit(trim(str_replace([';', ':', '|'], ' ', $value[1])), 191, '');
                    $otherAttributesArray[$value0] = Str::limit($value1);
                }

                if (count($otherAttributesArray)) {
                    // otherAttributes sanitized - sync attributes
                    foreach ($otherAttributesArray as $key => $value) {

                        // get attribute
                        if (!isset($attributes[$key])) {
                            $attributes[$key] = Attribute::create(['name' => $key]);
                        }
                        $attribute = $attributes[$key];

                        // get attribute value
                        if (!isset($attribute->attribute_values_key_by_name[$value])) {
                            AttributeValue::create(['name' => $value, 'attribute_id' => $attribute->id]);
                            $attribute->refresh();
                        }
                        $attributeValue = $attribute->attribute_values_key_by_name[$value];

                        $productOtherAttributes[] = $attribute->name;
                        $productOtherAttributeIDs[] = (int)$attribute->id;
                        $productOtherAttributeValues[] = $attributeValue->name;
                        $productOtherAttributeValueIDs[] = (int)$attributeValue->id;
                    }
                }
                $productAllAttributes = [];
                $productAllAttributeValues = [];
                foreach($productOtherAttributeIDs as $productOtherAttributeID) {
                    $productAllAttributes[] = $productOtherAttributeID;
                }
                foreach($productOtherAttributeValueIDs as $productOtherAttributeValueID) {
                    $productAllAttributeValues[] = $productOtherAttributeValueID;
                }
                $product->attributes()->syncWithoutDetaching($productAllAttributes);
                $product->attributeValues()->syncWithoutDetaching($productAllAttributeValues);

            }
            // ignore other sheets
            break;
        }
        $reader->close();

        return response()->json([
            'status' => 'true',
            'message' => 'Your products have been uploaded successfully!'
        ]);
    }

    public function smartupProducts(Request $request)
    {
        $this->checkPermissions();

        Artisan::call('synchro:smartup');

        return redirect()->route('voyager.import.index')->with([
            'message'    => 'Import completed',
            'alert-type' => 'success',
            'result' => Artisan::output(),
        ]);
    }

    public function error($message)
    {
        return redirect()->route('voyager.import.index')->with([
            'message'    => $message,
            'alert-type' => 'error',
        ]);
    }

    private function checkPermissions()
    {
        $this->authorize('browse_admin');
        return true;
    }
}
