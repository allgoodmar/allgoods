<?php

namespace Database\Factories;

use App\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AttributeValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $wordCount = mt_rand(1, 1);
        $title = Str::title(implode(' ', $this->faker->words($wordCount)));
        $attribute = Attribute::inRandomOrder()->first();
        return [
            'name' => $title,
            'slug' => Str::slug($title),
            'attribute_id' => $attribute->id,
        ];
    }
}
