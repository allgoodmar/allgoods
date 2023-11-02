<?php

namespace Database\Seeders;

use App\Publication;
use App\Pubrubric;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PublicationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('publications')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Publication::factory()->count(10)->create([
        //     'user_id' => 3,
        // ]);
        // Publication::factory()->count(50)->create();

        Publication::factory()->count(10)->create([
            'type' => Publication::TYPE_PROMOTION,
        ]);

        Publication::factory()->count(20)->create([
            'type' => Publication::TYPE_ARTICLE,
        ]);

        $names = [
            'Как выбрать телевизор',
            'Новинки электроники',
            'Какие лучше наушники',
            'Как выбрать смартфон',
        ];
        foreach ($names as $key => $name) {
            Publication::factory()->create([
                'name' => $name,
                'slug' => Str::slug($name),
                'type' => Publication::TYPE_ARTICLE,
                'created_at' => now()->addSeconds($key + 1),
            ]);
        }
    }
}
