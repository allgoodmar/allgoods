<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RubricFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $wordCount = mt_rand(1, 3);
        $title = Str::title(implode(' ', $this->faker->words($wordCount)));
        return [
            'name' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph,
            'body' => '<p>' . implode('</p><p>', $this->faker->paragraphs(4)) . '</p>',
            'status' => 1,
        ];
    }
}
