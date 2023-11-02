<?php

namespace Database\Seeders;

use App\AttributeValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AttributeValuesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Color
        AttributeValue::factory()->create([
            'attribute_id' => 2,
            'name' => 'Белый',
            'slug' => Str::slug('Белый'),
            'used_for_filter' => 1,
            'color' => '#ffffff',
            'is_light_color' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 2,
            'name' => 'Черный',
            'slug' => Str::slug('Черный'),
            'used_for_filter' => 1,
            'color' => '#000000',
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 2,
            'name' => 'Красный',
            'slug' => Str::slug('Красный'),
            'used_for_filter' => 1,
            'color' => '#DE1F16',
        ]);
        // AttributeValue::factory()->create([
        //     'attribute_id' => 2,
        //     'name' => 'Зеленый',
        //     'slug' => Str::slug('Зеленый'),
        //     'used_for_filter' => 1,
        //     'color' => '#8BC34C',
        // ]);
        AttributeValue::factory()->create([
            'attribute_id' => 2,
            'name' => 'Серый',
            'slug' => Str::slug('Серый'),
            'used_for_filter' => 1,
            'color' => '#BABAC0',
        ]);
        // AttributeValue::factory()->create([
        //     'attribute_id' => 2,
        //     'name' => 'Голубой',
        //     'slug' => Str::slug('Голубой'),
        //     'used_for_filter' => 1,
        //     'color' => '#73BFEB',
        // ]);
        AttributeValue::factory()->create([
            'attribute_id' => 2,
            'name' => 'Золотой',
            'slug' => Str::slug('Золотой'),
            'used_for_filter' => 1,
            'color' => '#FFD75F',
        ]);

        AttributeValue::factory()->create([
            'attribute_id' => 3,
            'name' => '8GB',
            'slug' => Str::slug('8GB'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 3,
            'name' => '16GB',
            'slug' => Str::slug('16GB'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 3,
            'name' => '32GB',
            'slug' => Str::slug('32GB'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 3,
            'name' => '64GB',
            'slug' => Str::slug('64GB'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 3,
            'name' => '128GB',
            'slug' => Str::slug('128GB'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 3,
            'name' => '256GB',
            'slug' => Str::slug('256GB'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 3,
            'name' => '512GB',
            'slug' => Str::slug('512GB'),
            'used_for_filter' => 1,
        ]);

        AttributeValue::factory()->create([
            'attribute_id' => 4,
            'name' => '4.9”',
            'slug' => Str::slug('4.9”'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 4,
            'name' => '5.0”',
            'slug' => Str::slug('5.0”'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 4,
            'name' => '5.1”',
            'slug' => Str::slug('5.1”'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 4,
            'name' => '5.5”',
            'slug' => Str::slug('5.5”'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 4,
            'name' => '6.0”',
            'slug' => Str::slug('6.0”'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 4,
            'name' => '6.5”',
            'slug' => Str::slug('6.5”'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 4,
            'name' => '7.0”',
            'slug' => Str::slug('7.0”'),
            'used_for_filter' => 1,
        ]);

        AttributeValue::factory()->create([
            'attribute_id' => 5,
            'name' => '24',
            'slug' => Str::slug('24'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 5,
            'name' => '27',
            'slug' => Str::slug('27'),
            'used_for_filter' => 1,
        ]);

        AttributeValue::factory()->create([
            'attribute_id' => 6,
            'name' => 'A1',
            'slug' => Str::slug('A1'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 6,
            'name' => 'A2',
            'slug' => Str::slug('A2'),
            'used_for_filter' => 1,
        ]);

        AttributeValue::factory()->create([
            'attribute_id' => 7,
            'name' => 'Есть',
            'slug' => Str::slug('Есть'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 7,
            'name' => 'Нет',
            'slug' => Str::slug('Нет'),
            'used_for_filter' => 1,
        ]);

        AttributeValue::factory()->create([
            'attribute_id' => 8,
            'name' => 'iOS 12',
            'slug' => Str::slug('iOS 12'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 8,
            'name' => 'iOS 13',
            'slug' => Str::slug('iOS 13'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 8,
            'name' => 'iOS 14',
            'slug' => Str::slug('iOS 14'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 8,
            'name' => 'Android 7',
            'slug' => Str::slug('Android 7'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 8,
            'name' => 'Android 8',
            'slug' => Str::slug('Android 8'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 8,
            'name' => 'Android 9',
            'slug' => Str::slug('Android 9'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 8,
            'name' => 'Android 10',
            'slug' => Str::slug('Android 10'),
            'used_for_filter' => 1,
        ]);
        AttributeValue::factory()->create([
            'attribute_id' => 8,
            'name' => 'Android 11',
            'slug' => Str::slug('Android 11'),
            'used_for_filter' => 1,
        ]);

        // Test
        // AttributeValue::factory()->count(5)->create([
        //     'attribute_id' => 3,
        //     'used_for_filter' => 1,
        // ]);

        // Random
        // AttributeValue::factory()->count(5)->create([
        //     'attribute_id' => 3,
        // ]);
    }
}
