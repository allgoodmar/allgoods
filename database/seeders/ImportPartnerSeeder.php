<?php

namespace Database\Seeders;

use App\ImportPartner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportPartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('import_partners')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        ImportPartner::create([
            'name' => 'Elmakon',
        ]);
        ImportPartner::create([
            'name' => 'Billz',
        ]);
        ImportPartner::create([
            'name' => 'Trendyol',
        ]);
    }
}
