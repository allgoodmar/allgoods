<?php

namespace App\Console\Commands;

use App\ImportPartner;
use App\Product;
use App\Services\ElmakonService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ElmakonProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elmakon:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Elmakon products';

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
        $elmakon = new ElmakonService();
        $response = $elmakon->getProducts();
        if ($response && $response->getStatusCode() == 200) {
            $json = json_decode($response->getBody()->getContents());
            if (empty($json->isSucceed) || empty($json->data) || !is_array($json->data)) {
                return 0;
            }
            $importPartner = ImportPartner::findOrFail(1);
            $importPartner->load('importPartnerMargins');
            $margins = $importPartner->importPartnerMargins;

            foreach ($json->data as $row) {
                $product = Product::firstOrNew([
                    'external_id' => $row->uuid,
                ], [
                    'status' => Product::STATUS_PENDING,
                    'sku' => '9999-' . $row->code,
                    'name' => $row->name,
                    'slug' => Str::slug($row->name),
                    'import_partner_id' => $importPartner->id,
                ]);

                // stock
                $product->in_stock = $row->totalQty;

                // price
                $percent = 0;
                $initialPrice = $row->price->price ?? 0;
                if ($initialPrice > 0) {
                    $margin = $margins->where('from', '<=', $initialPrice)->where('to', '>=', $initialPrice)->first();
                    if ($margin) {
                        $percent = $margin->percent;
                    }
                }
                $price = $initialPrice * (1 + $percent / 100);
                $product->price = $price;

                // save
                $product->save();
            }
        }
        return 0;
    }
}
