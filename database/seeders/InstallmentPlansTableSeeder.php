<?php

namespace Database\Seeders;

use App\InstallmentPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InstallmentPlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('installment_plans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        InstallmentPlan::create([
            'months' => 6,
        ]);
        InstallmentPlan::create([
            'months' => 12,
        ]);

    }
}
