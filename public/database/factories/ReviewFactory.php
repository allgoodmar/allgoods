<?php

namespace Database\Factories;

use App\Product;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $wordCount = mt_rand(1, 2);
        $title = Str::title(implode(' ', $this->faker->words($wordCount)));
        $product = Product::inRandomOrder()->first();
        $hasUser = mt_rand(0, 1);
        $review = [
            'name' => $title,
            'body' => $this->faker->paragraph,
            'status' => 1,
            'rating' => mt_rand(1, 5),
            'reviewable_type' => 'App\Product',
            'reviewable_id' => $product->id,
            'user_id' => ($hasUser) ? User::inRandomOrder()->first() : null,
        ];
        return $review;
    }
}
