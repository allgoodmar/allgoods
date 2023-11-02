<?php

namespace Database\Seeders;

use App\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('brands')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Brand::factory()->create([
            'name' => 'Apple',
            'slug' => Str::slug('Apple'),
            'image' => 'brands/001.png',
        ]);

        Brand::factory()->create([
            'name' => 'Samsung',
            'slug' => Str::slug('Samsung'),
            'image' => 'brands/002.png',
        ]);

        Brand::factory()->create([
            'name' => 'Honor',
            'slug' => Str::slug('Honor'),
            'image' => 'brands/003.png',
        ]);

        Brand::factory()->create([
            'name' => 'Xiaomi',
            'slug' => Str::slug('Xiaomi'),
            'image' => 'brands/004.png',
        ]);

        Brand::factory()->create([
            'name' => 'Huawei',
            'slug' => Str::slug('Huawei'),
            'image' => 'brands/005.png',
        ]);

        Brand::factory()->create([
            'name' => 'JBL',
            'slug' => Str::slug('JBL'),
            'image' => 'brands/006.png',
        ]);

        // Brand::factory()->create([
        //     'name' => 'LG',
        //     'slug' => Str::slug('LG'),
        //     'image' => 'brands/08.jpg',
        // ]);

        // Brand::factory()->create([
        //     'name' => 'Canon',
        //     'slug' => Str::slug('Canon'),
        //     'image' => 'brands/09.jpg',
        // ]);

        // Brand::factory()->create([
        //     'name' => 'HP',
        //     'slug' => Str::slug('HP'),
        //     'image' => 'brands/10.jpg',
        // ]);
    }
}
