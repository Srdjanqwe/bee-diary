<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\History;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $categories = Category::pluck('id');

        // return [
        //     'category_id' => $categories->random(),
        //     'no' => $this->faker->unique(true)->numberBetween(1,10)
        // ];
    }
}
