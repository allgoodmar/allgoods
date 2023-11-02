<?php

namespace App\Console\Commands;

use App\Brand;
use App\Category;
use App\Helpers\Helper;
use App\InstallmentPlan;
use App\Product;
use App\Services\Smartup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SynchroSmartup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synchro:smartup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize Smartup products';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Synchronization start');

        $smartup = new Smartup();
        $smartup->saveProductsFiles();

        $added = collect();
        $updated = collect();

        $allFiles = [];
        $productsFile = $allFiles[] = storage_path('app/smartup/products.json');
        $productsPriceFile = $allFiles[] = storage_path('app/smartup/products-price.json');
        $warehouseFiles = [];

        $warehouses = $smartup->getWarehouses();
        foreach($warehouses as $warehouse) {
            $warehouseFiles[$warehouse->code] = $allFiles[] = storage_path('app/smartup/warehouses/stock-' . $warehouse->code . '.json');
        }

        foreach($allFiles as $file) {
            if (!file_exists($file)) {
                $this->info('File not found!');
                return 0;
            }
        }

        // udpate products
        $brands = Brand::all()->keyBy('name');
        $categories = Category::all()->keyBy('name');
        $installmentPlan = InstallmentPlan::first();
        $products = file_get_contents($productsFile);
        $products = json_decode($products);
        if (empty($products->status) || $products->status != 'success' || empty($products->result) || !is_array($products->result)) {
            $this->info('Products file content error');
            return 0;
        }
        foreach($products->result as $importProduct) {
            $name = isset($importProduct->name) ? trim($importProduct->name) : '';
            $sku = isset($importProduct->code) ? (string)trim($importProduct->code) : '';
            $externalID = isset($importProduct->product_id) ? trim($importProduct->product_id) : '';
            $brandName = isset($importProduct->producer_name) ? trim($importProduct->producer_name) : '';
            $orderNo = isset($importProduct->order_no) ? (int)trim($importProduct->order_no) : 0;
            $barcode = isset($importProduct->barcodes[0]) ? trim($importProduct->barcodes[0]) : '';
            // $status = Product::STATUS_ACTIVE;
            $status = Product::STATUS_PENDING;
            // $status = isset($importProduct->state) ? trim($importProduct->state) : 0;

            $categoryName = '';
            $importCategoryName = '';
            $importProductGroups = (isset($importProduct->product_groups) && is_array($importProduct->product_groups)) ? $importProduct->product_groups : [];
            foreach($importProductGroups as $importProductGroup) {
                if (!isset($importProductGroup->product_group_id) || !isset($importProductGroup->product_type_name)) {
                    continue;
                }
                if ($importProductGroup->product_group_id == 3) {
                    // 3 - product category
                    $importCategoryName = $categoryName = trim($importProductGroup->product_type_name);
                }
            }

            // ignore if name empty
            if (!$name || !$sku) {
                continue;
            }

            $name = Str::limit($name, 191, '');
            $sku = Str::limit($sku, 191, '');
            $brandName = Str::limit($brandName, 191, '');
            // if (!array_key_exists($status, Product::statuses())) {
            //     $status = Product::STATUS_PENDING;
            // }

            // $description = Str::limit($description, 65535, '');

            // brand
            if (!isset($brands[$brandName])) {
                $brands[$brandName] = Brand::create(['name' => $brandName, 'slug' => Str::slug($brandName), 'status' => Brand::STATUS_ACTIVE]);
            }
            $brandID =  $brands[$brandName]->id;

            // categories
            if ($importCategoryName) {
                $categoriesSynchroNames = $smartup->getCategoriesSynchroNames();
                if (isset($categoriesSynchroNames[$importCategoryName])) {
                    $categoryName = $categoriesSynchroNames[$importCategoryName];
                }
            }

            $categoryName = Str::limit($categoryName, 191, '');
            if (!isset($categories[$categoryName])) {
                $categories[$categoryName] = Category::create(['name' => $categoryName, 'slug' => Str::slug($categoryName), 'status' => Category::STATUS_ACTIVE]);
            }
            $categoryIDs = Category::parentIDs($categories[$categoryName]);

            if (isset($categories[$categoryName . ' ' . $brandName])) {
                $categoryIDs[] = $categories[$categoryName . ' ' . $brandName]->id;
            }

            $findData = [
                'sku' => $sku,
            ];
            $createData = [
                'slug' => Str::slug($name),
            ];
            $updateData = [
                'name' => $name,
                'brand_id' => $brandID,
                'external_id' => $externalID,
                'barcode' => $barcode,
                'order' => $orderNo,
                'installment_price' => 0,
                'price' => 0,
                'sale_price' => 0,
                'in_stock' => 0,
                // 'status' => $status,
            ];

            $query = Product::query();
            foreach($findData as $key => $value) {
                $query->where($key, $value);
            }
            $product = $query->first();
            // create if no product
            if (!$product) {
                // add new product
                $createData = array_merge($createData, $updateData, $findData);
                $createData['status'] = $updateData['status'] = Product::STATUS_PENDING;
                $product = Product::create($createData);

                // categories
                if ($categoryIDs) {
                    $product->categories()->sync($categoryIDs);
                }

                // installment plans
                if ($installmentPlan) {
                    $product->installmentPlans()->sync([$installmentPlan->id]);
                }

                $added->push($product->id);
            } else {
                // update product
                $product->update($updateData);
                $updated->push($product->id);
            }


        }

        // udpate products price
        $productsPrice = file_get_contents($productsPriceFile);
        $productsPrice = json_decode($productsPrice);
        if (empty($productsPrice->status) || $productsPrice->status != 'success' || empty($productsPrice->result) || !is_array($productsPrice->result)) {
            $this->info('Products price file content error');
            return 0;
        }
        foreach($productsPrice->result as $importProductPrice) {
            // $name = isset($importProductPrice->product_name) ? trim($importProductPrice->product_name) : '';
            $sku = isset($importProductPrice->product_code) ? (string)trim($importProductPrice->product_code) : '';
            // $externalID = isset($importProductPrice->product_id) ? trim($importProductPrice->product_id) : '';
            $priceTypeID = isset($importProductPrice->price_type_id) ? trim($importProductPrice->price_type_id) : '';
            // $priceTypeName = isset($importProductPrice->price_type_name) ? trim($importProductPrice->price_type_name) : '';
            $price = isset($importProductPrice->price) ? trim($importProductPrice->price) : 0;

            if (!$sku || !$priceTypeID || !$price) {
                continue;
            }

            $minPricePerMonth = 0;
            $priceTypeField = '';
            switch($priceTypeID) {
                case '2':
                    $priceTypeField = 'price';
                    break;
                case '301':
                    $priceTypeField = 'installment_price';
                    $minPricePerMonth = $price / 12;
                    break;
            }
            if (!$priceTypeField) {
                continue;
            }

            $updateData = [
                $priceTypeField => $price,
            ];
            if ($minPricePerMonth > 0) {
                $updateData['min_price_per_month'] = $minPricePerMonth;
            }
            Product::where('sku', $sku)->update($updateData);
        }

        // udpate products stock
        $productsStock = [];
        // Reset stock to 0
        DB::table('product_warehouse')->update([
            'quantity' => 0,
        ]);
        foreach($warehouseFiles as $warehouseCode => $file) {

            if (!file_exists($file)) {
                continue;
            }

            $warehouse = $warehouses->where('code', $warehouseCode)->first();
            if (!$warehouse) {
                continue;
            }

            $stock = file_get_contents($file);
            if (!$stock) {
                continue;
            }

            $stock = json_decode($stock);

            if (empty($stock->status) || $stock->status != 'success' || empty($stock->result) || !is_array($stock->result)) {
                continue;
                // return $this->error('Stock file content error');
            }

            foreach($stock->result as $importProductStock) {
                $sku = isset($importProductStock->product_code) ? (string)trim($importProductStock->product_code) : '';
                $quantity = isset($importProductStock->free_quant) ? (int)trim($importProductStock->free_quant) : 0;

                if (!$sku || !$quantity) {
                    continue;
                }

                $product = Product::where('sku', $sku)->first();
                if (!$product) {
                    continue;
                }

                $product->warehouses()->syncWithoutDetaching([$warehouse->id => ['quantity' => $quantity]]);

                // if (!isset($productsStock[$sku])) {
                //     $productsStock[$sku] = [
                //         'sku' => $sku,
                //         'quantity' => 0,
                //     ];
                // }
                // $productsStock[$sku]['quantity'] += $quantity;
            }
        }

        // foreach ($productsStock as $productsStockOne) {
        //     Product::where('sku', $productsStockOne['sku'])->update([
        //         'in_stock' => $productsStockOne['quantity'],
        //     ]);
        // }

        $this->info('Added: ' . $added->count());
        $this->info('Updated: ' . $updated->count());

        $this->info('Synchronization end');
        return 0;
    }
}
