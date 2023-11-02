<?php

namespace App\Console\Commands;

use App\Product;
use App\Referal;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UpdateStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status field of products from merchant';

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
        Product::where([['seller_id', '<>', null],['is_active_from_seller', '2']])->chunk(200, function($products) {
            foreach($products as $product) {
                $product->status = 0;
                $product->saveQuietly();
            }
        });
        return 0;
    }
}
