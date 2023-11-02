<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\ImportPartner;
use App\Product;
use App\Services\BillzService;
use Illuminate\Http\Request;

class BillzController extends Controller
{
    public function getToken()
    {
        $service = new BillzService();
        $token = $service->generateToken();

        return $token;
    }

    public function getProducts()
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

        $data = json_decode(json_encode($body));
        $data = $service->sendReq($data);

        return $data;
    }

    public function storeProducts(Request $request)
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
        // $arr = json_decode($arr, true);
        $arr = json_decode($arr);

        $importPartner = ImportPartner::findOrFail(2);

        if ($dataBillz->getStatusCode() == 200) {
            foreach ($arr->result as $key => $value) {
                $count = Product::where('sku', '8888-00-'.$value->sku)->count();
                if ($count == 0) {
                    $p = Product::create([
                        'external_id' => $value->ID,
                        'status' => Product::STATUS_PENDING,
                        'sku' => '8888-00-'.$value->sku,
                        'name' => $value->name,
                        'price' => $value->price,
                        'in_stock' => $value->qty,
                        'import_partner_id' => $importPartner->id
                    ]);

                    if (!empty($value->imageUrls)) {
                        foreach ($value->imageUrls as $key2 => $value2) {
                            $org_image = str_replace('_square', '', $value2->url);
                            Helper::storeImageFromUrl($org_image, $p, 'image', 'products');
                        }
                    }
                }
            }
        }
    }
}
