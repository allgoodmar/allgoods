<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $word = Str::title($this->faker->word);
        return [
            'name' => $word,
            'slug' => Str::slug($word),
            'description' => $this->faker->text(),
            'body' => '<p>' . implode('</p><p>', $this->faker->paragraphs(4)) . '</p>',
            'status' => 1,
        ];
    }
}
