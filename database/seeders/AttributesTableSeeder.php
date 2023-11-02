<?php

namespace Database\Seeders;

use App\Attribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AttributesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Size
        Attribute::factory()->create([
            'name' => 'Размер',
            'slug' => 'size',
            'used_for_filter' => 0,
            'type' => Attribute::TYPE_BUTTONS,
        ]);

        // Color
        Attribute::factory()->create([
            'name' => 'Цвет',
            'slug' => 'color',
            'used_for_filter' => 1,
            'type' => Attribute::TYPE_COLORS,
        ]);

        Attribute::factory()->create([
            'name' => 'Память',
            'slug' => Str::slug('Память'),
            'used_for_filter' => 1,
            'type' => Attribute::TYPE_LIST,
        ]);

        Attribute::factory()->create([
            'name' => 'Диагональ',
            'slug' => Str::slug('Диагональ'),
            'used_for_filter' => 1,
            'type' => Attribute::TYPE_LIST,
        ]);

        Attribute::factory()->create([
            'name' => 'Экран',
            'slug' => Str::slug('Экран'),
            'used_for_filter' => 0,
            'type' => Attribute::TYPE_LIST,
        ]);

        Attribute::factory()->create([
            'name' => 'Тип процессора',
            'slug' => Str::slug('Тип процессора'),
            'used_for_filter' => 0,
            'type' => Attribute::TYPE_LIST,
        ]);

        Attribute::factory()->create([
            'name' => 'Отпечаток пальца',
            'slug' => Str::slug('Отпечаток пальца'),
            'used_for_filter' => 0,
            'type' => Attribute::TYPE_LIST,
        ]);

        Attribute::factory()->create([
            'name' => 'Версия ОС',
            'slug' => Str::slug('Версия ОС'),
            'used_for_filter' => 0,
            'type' => Attribute::TYPE_LIST,
        ]);

        // Test
        // Attribute::factory()->create([
        //     'name' => 'Test',
        //     'used_for_filter' => 1,
        //     'type' => Attribute::TYPE_LIST,
        // ]);

        // Random
        //Attribute::factory()->count(3)->create();
    }
}
