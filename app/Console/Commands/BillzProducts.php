<?php

namespace App\Console\Commands;

use App\BillzPartner;
use App\Helpers\Helper;
use App\ImportPartner;
use App\Product;
use App\Services\BillzService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BillzProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billz:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Billz products';

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
        $service = new BillzService();
        $body = [
            "jsonrpc" => "2.0",
            "method" => "products.get",
            "params" => [
                "LastUpdatedDate" =>  "2018-03-21T18:19:25Z",
                "WithProductPhotoOnly" => 0,
                "IncludeEmptyStocks" => 0
            ],
            "id" => "1"
        ];
        $body = json_decode(json_encode($body));
        $dataBillz = $service->sendReq($body);
        $arr = $dataBillz->getBody()->getContents();
        $arr = json_decode($arr);

        $importPartner = ImportPartner::findOrFail(2);

        // Billz
        $percent = BillzPartner::select('percent')->where('code', BillzPartner::BILLZ_ALLSTORE)->first();

        if ($dataBillz->getStatusCode() == 200) {
            foreach ($arr->result as $key => $value) {
                $count = Product::where('sku', BillzPartner::BILLZ_ALLSTORE.'-'.$value->sku)->count();

                if (empty($value->properties->DESCRIPTION)) {
                    $body = '';
                }else{
                    $body = $value->properties->DESCRIPTION;
                }

                if ($count == 0) {
                    if (!empty($value->imageUrls)) {
                        if ($value->price != 0) {

                            $result_after_percented = ($percent->percent / 100) * $value->price;
                            $new_price = $value->price + $result_after_percented;

                            $p = Product::create([
                                'slug' => Str::slug($value->name),
                                'external_id' => $value->ID,
                                'status' => Product::STATUS_PENDING,
                                'barcode' => $value->barCode,
                                'sku' => BillzPartner::BILLZ_ALLSTORE.'-'.$value->sku,
                                'name' => $value->name,
                                'price' => $new_price,
                                'in_stock' => $value->qty,
                                'body' => $body,
                                'import_partner_id' => $importPartner->id
                            ]);

                            $org_image = str_replace('_square', '', $value->imageUrls[0]->url);
                            Helper::storeImageFromUrl($org_image, $p, 'image', 'products', Product::$imgSizes);

                            $filtered = [];
                            foreach ($value->imageUrls as $key => $url) {
                                $filtered[] = str_replace('_square', '', $url->url);
                            }
                            Helper::storeImagesFromUrl($filtered, $p, 'images', 'products', Product::$imgSizes);
                        }
                    }
                }else{
                    if ($value->price != 0) {

                        $result_after_percented = ($percent->percent / 100) * $value->price;
                        $new_price = $value->price + $result_after_percented;

                        Product::where('sku', BillzPartner::BILLZ_ALLSTORE.'-'.$value->sku)->update([
                            'barcode' => $value->barCode,
                            'sku' => BillzPartner::BILLZ_ALLSTORE.'-'.$value->sku,
                            'name' => $value->name,
                            'price' => $new_price,
                            'in_stock' => $value->qty,
                            'body' => $value->properties->DESCRIPTION,
                        ]);
                    }
                }
            }
        }
    }
}
