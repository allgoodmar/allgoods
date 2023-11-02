<?php

namespace Database\Seeders;

use App\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $category = Category::factory()->create([
            'name' => 'Электроника',
            'slug' => Str::slug('Электроника'),
            'image' => 'categories/001.png',
            'show_in' => 1,
            'order' => 10,
        ]);

            $category1 = Category::factory()->create([
                'parent_id' => $category->id,
                'name' => 'Телефоны и смарт-часы',
                'slug' => Str::slug('Телефоны и смарт-часы'),
                'show_in' => 2,
            ]);

                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Смартфоны',
                    'slug' => Str::slug('Смартфоны'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Кнопочные телефоны',
                    'slug' => Str::slug('Кнопочные телефоны'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Смарт-часы',
                    'slug' => Str::slug('Смарт-часы'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Фитнес-браслеты',
                    'slug' => Str::slug('Фитнес-браслеты'),
                    'show_in' => 2,
                ]);

            $category1 = Category::factory()->create([
                'parent_id' => $category->id,
                'name' => 'Аудиотехника',
                'slug' => Str::slug('Аудиотехника'),
                'show_in' => 2,
            ]);

                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Наушники и гарнитуры',
                    'slug' => Str::slug('Наушники и гарнитуры'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Акустика и колонки',
                    'slug' => Str::slug('Акустика и колонки'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Портативная акустика',
                    'slug' => Str::slug('Портативная акустика'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Микрофоны',
                    'slug' => Str::slug('Микрофоны'),
                    'show_in' => 2,
                ]);

            $category1 = Category::factory()->create([
                'parent_id' => $category->id,
                'name' => 'Офисная техника',
                'slug' => Str::slug('Офисная техника'),
                'show_in' => 2,
            ]);

                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Принтеры',
                    'slug' => Str::slug('Принтеры'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'МФУ',
                    'slug' => Str::slug('МФУ'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Расходные материалы',
                    'slug' => Str::slug('Расходные материалы'),
                    'show_in' => 2,
                ]);

        $category = Category::factory()->create([
            'name' => 'Бытовая техника',
            'slug' => Str::slug('Бытовая техника'),
            'image' => 'categories/002.png',
            'show_in' => 1,
            'order' => 20,
        ]);

            $category1 = Category::factory()->create([
                'parent_id' => $category->id,
                'name' => 'Крупная техника',
                'slug' => Str::slug('Крупная техника'),
                'show_in' => 2,
            ]);

                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Холодильники',
                    'slug' => Str::slug('Холодильники'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Стиральные машины',
                    'slug' => Str::slug('Стиральные машины'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Плиты',
                    'slug' => Str::slug('Плиты'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Посудомоечные машины',
                    'slug' => Str::slug('Посудомоечные машины'),
                    'show_in' => 2,
                ]);

            $category1 = Category::factory()->create([
                'parent_id' => $category->id,
                'name' => 'Техника для кухни',
                'slug' => Str::slug('Техника для кухни'),
                'show_in' => 2,
            ]);

                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Кофеварки и кофемашины',
                    'slug' => Str::slug('Кофеварки и кофемашины'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Электрические чайники',
                    'slug' => Str::slug('Электрические чайники'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Миксеры',
                    'slug' => Str::slug('Миксеры'),
                    'show_in' => 2,
                ]);

            $category1 = Category::factory()->create([
                'parent_id' => $category->id,
                'name' => 'Техника для дома',
                'slug' => Str::slug('Техника для дома'),
                'show_in' => 2,
            ]);

                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Пылесосы',
                    'slug' => Str::slug('Пылесосы'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Утюги',
                    'slug' => Str::slug('Утюги'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Швейные машины',
                    'slug' => Str::slug('Швейные машины'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Пароочистители',
                    'slug' => Str::slug('Пароочистители'),
                    'show_in' => 2,
                ]);

            $category1 = Category::factory()->create([
                'parent_id' => $category->id,
                'name' => 'Климатическая техника',
                'slug' => Str::slug('Климатическая техника'),
                'show_in' => 2,
            ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Кондиционеры',
                    'slug' => Str::slug('Кондиционеры'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Вентиляторы',
                    'slug' => Str::slug('Вентиляторы'),
                    'show_in' => 2,
                ]);
                Category::factory()->create([
                    'parent_id' => $category1->id,
                    'name' => 'Увлажнители воздуха',
                    'slug' => Str::slug('Увлажнители воздуха'),
                    'show_in' => 2,
                ]);

        $category = Category::factory()->create([
            'name' => 'Одежда',
            'slug' => Str::slug('Одежда'),
            'image' => 'categories/003.png',
            'show_in' => 1,
            'order' => 30,
        ]);

        $category = Category::factory()->create([
            'name' => 'Обувь',
            'slug' => Str::slug('Обувь'),
            'image' => 'categories/004.png',
            'show_in' => 1,
            'order' => 40,
        ]);

        $category = Category::factory()->create([
            'name' => 'Дом и сад',
            'slug' => Str::slug('Дом и сад'),
            'image' => 'categories/005.png',
            'show_in' => 1,
            'order' => 50,
        ]);

        $category = Category::factory()->create([
            'name' => 'Красота и здоровье',
            'slug' => Str::slug('Красота и здоровье'),
            'image' => 'categories/006.png',
            'show_in' => 1,
            'order' => 60,
        ]);


            // Category::factory()->create([
            //     'parent_id' => $category->id,
            //     'name' => '',
            //     'slug' => Str::slug(''),
            //     'show_in' => 2,
            // ]);


        // Category::factory()->count(10)->create();
        // Category::factory()->count(5)->create([
        //     'parent_id' => Category::inRandomOrder()->first()->id,
        // ]);
        // Category::factory()->count(6)->create([
        //     'parent_id' => Category::inRandomOrder()->first()->id,
        // ]);
        // Category::factory()->count(8)->create([
        //     'parent_id' => Category::inRandomOrder()->first()->id,
        // ]);
        // Category::factory()->count(5)->create([
        //     'parent_id' => Category::inRandomOrder()->first()->id,
        // ]);
        // Category::factory()->count(6)->create([
        //     'parent_id' => Category::inRandomOrder()->first()->id,
        // ]);

    }
}
