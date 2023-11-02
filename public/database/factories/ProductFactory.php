<?php

namespace Database\Factories;

use App\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $wordCount = mt_rand(2, 8);
        $title = Str::title(implode(' ', $this->faker->words($wordCount)));
        // $category = Category::inRandomOrder()->first();
        $price = mt_rand(50, 500) * 1000;
        $imgNumber = mt_rand(1, 6);
        $random10 = mt_rand(0, 9);
        $brand = Brand::inRandomOrder()->first();

        $product = [
            'name' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph,
            'body' => '<p>' . implode('</p><p>', $this->faker->paragraphs(4)) . '</p>',
            'status' => 1,
            'specifications' => '<p>' . implode('</p><p>', $this->faker->paragraphs(4)) . '</p>',
            // 'category_id' => $category->id,
            'brand_id' => $brand->id,
            'installment_price' => $price * 1.2,
            'price' => $price,
            'sale_price' => in_array($random10, [6, 7]) ? $price * 0.9 : 0,
            'image' => 'products/0' . $imgNumber . '.png',
            'images' => '["products//gallery-01.png","products//gallery-02.png","products//gallery-03.png"]',
            'in_stock' => mt_rand(0, 9),
            'is_bestseller' => $random10 == 9 ? 1 : 0,
            // 'is_featured' => $random10 == 9 ? 1 : 0,
            'is_new' => $random10 == 8 ? 1 : 0,
            'is_promotion' => $random10 == 7 ? 1 : 0,
            'sku' => $this->faker->uuid,
        ];

        return $product;
    }
}
