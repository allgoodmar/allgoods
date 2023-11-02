<?php

namespace Database\Seeders;

use App\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehousesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('warehouses')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $warehouses = ['100', 'AN01', 'FA01', 'FA03', '05', 'NM01', 'M27',];

        foreach ($warehouses as $warehouse) {
            Warehouse::create([
                'code' => $warehouse,
                'region_id' => 14,
            ]);
        }

    }
}
