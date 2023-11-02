<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:products {task=price}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update products fields';

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
        $task = $this->argument('task');
        switch ($task) {
            case 'price':
                return $this->updatePrice();
                break;
            case 'rating':
                return $this->updateRating();
                break;
        }
    }

    private function updatePrice()
    {
        $dateTime = now()->subHour();
        Product::where('updated_at', '>', $dateTime)->chunk(200, function($products) {
            foreach($products as $product) {
                $partnersPrices = Helper::partnersPrices($product->current_price);
                $productMinPricePerMonth = $product->current_price;
                $productMinPricePerMonthDuration = 0;
                foreach ($partnersPrices as $item) {
                    if (empty($item['prices'])) {
                        continue;
                    }
                    foreach ($item['prices'] as $key => $itemPrice) {
                        if ($itemPrice['duration'] <= 12 && $itemPrice['price_per_month'] < $productMinPricePerMonth) {
                            $productMinPricePerMonth = $itemPrice['price_per_month'];
                            $productMinPricePerMonthDuration = $itemPrice['duration'];
                        }
                    }
                }
                $product->min_price_per_month = $productMinPricePerMonth;
                $product->min_price_per_month_duration = $productMinPricePerMonthDuration;

                $product->saveQuietly();
            }
        });
        return 0;
    }

    private function updateRating()
    {
        Product::with(['reviews'])->chunk(200, function($products) {
            // write products
            foreach($products as $product) {
                $product->rating = $product->rating_avg;
                $product->active_reviews_count = $product->rating_count;
                $product->saveQuietly();
            }
        });
        return 0;
    }
}
