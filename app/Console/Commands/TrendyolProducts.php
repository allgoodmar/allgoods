<?php

namespace App\Console\Commands;

use App\Attribute;
use App\Brand;
use App\Category;
use App\Helpers\Helper;
use App\ImportPartner;
use App\Product;
use App\ProductGroup;
use App\Services\TrendyolService;
use App\TrendyolCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Throwable;

class TrendyolProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trendyol:products {barcode?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Trendyol products';

    private $cacheKey = 'trendyol-last-page';

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
        $trendyol = new TrendyolService();
        $page = Cache::get($this->cacheKey, 1);
        if ($page > 2500) {
            $page = 1;
        }

        // update cache page
        Cache::put($this->cacheKey, $page + 1, 7200);

        $barcode = $this->argument('barcode');
        if ($barcode) {
            $response = $trendyol->offers(1, $barcode);
        } else {
            $response = $trendyol->offers($page);
        }

        if ($response && $response->getStatusCode() == 200) {
            $json = json_decode($response->getBody()->getContents());

            if (!is_array($json)) {
                return 0;
            } elseif ($json === []) {
                Cache::put($this->cacheKey, 1, 7200);
                return 0;
            }

            $usdRate = (int)setting('currency.usd');
            if ($usdRate <= 0) {
                $usdRate = 1;
            }

            $importPartner = ImportPartner::findOrFail(3);
            $importPartner->load('importPartnerMargins');
            $margins = $importPartner->importPartnerMargins;

            $brands = Brand::select(['id', 'name'])->get();
            $trendyolCategories = TrendyolCategory::with('categories')->get();

            $colorAttribute = Attribute::where('slug', 'color')->with('attributeValues')->firstOrFail();
            $sizeAttribute = Attribute::where('slug', 'size')->with('attributeValues')->firstOrFail();

            foreach ($json as $row) {
                $product = Product::firstOrNew([
                    'barcode' => $row->barcode,
                    'import_partner_id' => $importPartner->id,
                ], [
                    'status' => Product::STATUS_PENDING,
                    'sku' => $row->modelNo,
                    'name' => 'Trendyol ' . $row->name . ' ' . $row->color . ' ' . $row->size,
                    'slug' => Str::slug($row->name . ' ' . $row->color . ' ' . $row->size),
                    'gender' => $row->gender ?? '',
                    'age_group' => $row->ageGroup ?? '',
                ]);

                // stock
                $product->in_stock = $row->stockQuantity;

                // set not hidden
                $product->is_hidden = 0;

                // price
                $percent = 0;
                // $initialPrice = $row->price ?? 0;
                $initialPrice = $row->originalSalesPrice ?? $row->price ?? 0;
                if ($initialPrice > 0) {
                    $margin = $margins->where('from', '<=', $initialPrice)->where('to', '>=', $initialPrice)->first();
                    if ($margin) {
                        $percent = $margin->percent;
                    }
                }
                $price = $initialPrice * (1 + $percent / 100) * $usdRate;
                $product->price = $price;

                // brand
                $brand = $brands->where('name', $row->brand)->first();
                if (!$brand) {
                    $brand = Brand::firstOrCreate([
                        'name' => $row->brand,
                    ]);
                }
                $product->brand_id = $brand->id;

                // save
                $product->save();

                // categories
                $trendyolCategory = $trendyolCategories->where('name', $row->subCategory)->first();
                if (!$trendyolCategory) {
                    $trendyolCategory = TrendyolCategory::firstOrCreate([
                        'name' => $row->subCategory,
                    ]);
                }
                $categoryIDs = $trendyolCategory->categories->pluck('id');
                $product->categories()->syncWithoutDetaching($categoryIDs);

                // attributes color
                $colorAttributeValue = $colorAttribute->attributeValues()->where('name', $row->color)->first();
                if (!$colorAttributeValue) {
                    $colorAttributeValue = $colorAttribute->attributeValues()->create([
                        'name' => $row->color,
                        'slug' => Str::slug($row->color),
                    ]);
                }
                $product->attributes()->syncWithoutDetaching([$colorAttribute->id]);
                $product->attributeValues()->syncWithoutDetaching($colorAttributeValue->id);

                // attributes size
                $sizeAttributeValue = $sizeAttribute->attributeValues()->where('name', $row->size)->first();
                if (!$sizeAttributeValue) {
                    $sizeAttributeValue = $sizeAttribute->attributeValues()->create([
                        'name' => $row->size,
                        'slug' => Str::slug($row->size),
                    ]);
                }
                $product->attributes()->syncWithoutDetaching($sizeAttribute->id);
                $product->attributeValues()->syncWithoutDetaching($sizeAttributeValue->id);

                // check image
                if ($product->wasRecentlyCreated && $row->imageUrl) {
                    Helper::storeImageFromUrl($row->imageUrl, $product, 'image', 'products', Product::$imgSizes);
                }

                // product groups
                $similarProducts = Product::where('sku', $product->sku)->where('import_partner_id', $importPartner->id)->get();
                if ($similarProducts->count() > 1) {
                    $productGroup = ProductGroup::firstOrCreate([
                        'unique_code' => 'trendyol-' . $product->sku,
                    ], [
                        'name' => 'Trendyol - ' . $product->sku,
                    ]);
                    if ($productGroup->wasRecentlyCreated) {
                        $productGroup->attributes()->sync([$colorAttribute->id => [
                            'type' => ProductGroup::ATTRIBUTE_TYPE_IMAGES,
                        ], $sizeAttribute->id]);
                    }

                    // sync attribute value size
                    $productGroup->attributeValues()->syncWithoutDetaching([$sizeAttributeValue->id, $colorAttributeValue->id]);

                    // sync attribute value color
                    $colorAttributeValueWithPivot = $productGroup->attributeValues()->where('attribute_values.id', $colorAttributeValue->id)->first();
                    if ($colorAttributeValueWithPivot && $colorAttributeValueWithPivot->pivot->image == '') {
                        $dir = 'attribute-value-product-group/' . $productGroup->id . '/' . $colorAttributeValueWithPivot->id;
                        $imgPath = $product->image;
                        $newImgPath = $dir . '/' . $imgPath;
                        if (Storage::disk('public')->exists($newImgPath)) {
                            Storage::disk('public')->delete($newImgPath);
                        }
                        Storage::disk('public')->copy($imgPath, $newImgPath);
                        try {
                            $image = Image::make(Storage::disk('public')->path($newImgPath));
                            if ($image) {
                                $image->fit(Product::$imgSizes['medium'][0], Product::$imgSizes['medium'][1])->save();
                            }
                        } catch (Throwable $th) {
                            // Log::info($th->getMessage());
                        }
                        $productGroup->attributeValues()->syncWithoutDetaching([$colorAttributeValueWithPivot->id => [
                            'image' => $newImgPath,
                        ]]);
                    }

                    // update product group ids
                    Product::whereIn('id', $similarProducts->pluck('id'))->update(['product_group_id' => $productGroup->id]);
                    Product::where('id', '!=', $product->id)->where('sku', $product->sku)->update(['is_hidden' => 1]);

                    // bugfix
                    if ($similarProducts->count() == 2) {
                        $firstProduct = $similarProducts->where('id', '!=', $product->id)->first();
                        if ($firstProduct) {
                            $firstProductColorAttributeValue = $firstProduct->attributeValues()->where('attribute_values.attribute_id', $colorAttribute->id)->first();
                            $colorAttributeValueWithPivot = $productGroup->attributeValues()->where('attribute_values.id', $firstProductColorAttributeValue->id)->first();
                            if ($colorAttributeValueWithPivot && $colorAttributeValueWithPivot->pivot->image == '') {
                                $dir = 'attribute-value-product-group/' . $productGroup->id . '/' . $colorAttributeValueWithPivot->id;
                                $imgPath = $firstProduct->image;
                                $newImgPath = $dir . '/' . $imgPath;
                                if (Storage::disk('public')->exists($newImgPath)) {
                                    Storage::disk('public')->delete($newImgPath);
                                }
                                Storage::disk('public')->copy($imgPath, $newImgPath);
                                try {
                                    $image = Image::make(Storage::disk('public')->path($newImgPath));
                                    if ($image) {
                                        $image->fit(Product::$imgSizes['medium'][0], Product::$imgSizes['medium'][1])->save();
                                    }
                                } catch (Throwable $th) {
                                    // Log::info($th->getMessage());
                                }
                                $productGroup->attributeValues()->syncWithoutDetaching([$colorAttributeValueWithPivot->id => [
                                    'image' => $newImgPath,
                                ]]);
                            }
                        }
                    }
                }
            }

        }
        return 0;
    }
}
