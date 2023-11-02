<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StaticTextFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $word = Str::ucfirst(implode(' ', $this->faker->words(2)));
        return [
            'name' => $word,
            'key' => Str::slug($word),
            'description' => $this->faker->sentence,
        ];
    }
}
