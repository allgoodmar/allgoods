<?php

namespace App\Console\Commands;

use App\Product;
use Illuminate\Console\Command;

class NumberOfSalesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:numberofsales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make number of sales';

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
        $products = Product::orderBy('id', 'DESC')->get();

        foreach ($products as $key => $value) {
            $rand = rand(30, 150);
            Product::where('id', $value->id)->update([
                'number_of_sales' => $rand
            ]);
        }

        return 0;
    }
}
