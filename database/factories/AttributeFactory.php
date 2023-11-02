<?php

namespace Database\Factories;

use App\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AttributeFactory extends Factory
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
        return [
            'name' => $title,
            'slug' => Str::slug($title),
            'type' => Attribute::TYPE_LIST,
        ];
    }
}
