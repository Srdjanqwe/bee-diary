<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\History>
 */
class HistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $posts = Post::all()->pluck('id');
        // $posts = Post::pluck('id');

        return [
            'post_id'=> $posts->random(),

            'content' => $this->faker->paragraphs(3, true),
            'inspection_date' => $this->faker->dateTimeInInterval('-1 week', '+3days')
        ];

        
    }
}
