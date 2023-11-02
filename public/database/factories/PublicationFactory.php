<?php

namespace Database\Factories;

use App\Publication;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PublicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $word = Str::title($this->faker->sentence);
        // $rubric = Rubric::inRandomOrder()->first();
        $data = [
            'name' => $word,
            'slug' => Str::slug($word),
            'description' => $this->faker->paragraph,
            'body' => '<p>' . implode('</p><p>', $this->faker->paragraphs(6)) . '</p>',
            'status' => 1,
            'type' => Arr::random(array_keys(Publication::types())), //mt_rand(0, 4),
            //'rubric_id' => $rubric->id,
            'image' => 'publications/00' . mt_rand(1, 4) . '.jpg',
        ];

        return $data;
    }
}
