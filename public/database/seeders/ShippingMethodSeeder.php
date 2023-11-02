<?php

namespace Database\Seeders;

use App\ShippingMethod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShippingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('shipping_methods')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        ShippingMethod::create([
            'name' => 'Standard',
            'price' => 15000,
            'status' => 1,
        ]);
    }
}
