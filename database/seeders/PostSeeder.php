<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\History;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //  Post::factory(20)
        //     ->state(new Sequence(
        //         [
        //             'category_id' => '1'
        //         ],
        //         [
        //             'category_id' => '2'
        //         ],
        //     ))    
        // ->create();

        // Post::factory(10)->create();

        // 3 = random_int(1,5);

        // Post::create(['no' => '1', 'category_id' => '1']);
        // Post::create(['no' => '1', 'category_id' => '2']);
        // Post::create(['no' => '2', 'category_id' => '1']);
        // Post::create(['no' => '2', 'category_id' => '2']);
        // Post::create(['no' => '3', 'category_id' => '1']);
        // Post::create(['no' => '3', 'category_id' => '2']);
        // Post::create(['no' => '4', 'category_id' => '1']);
        // Post::create(['no' => '4', 'category_id' => '2']);
        // Post::create(['no' => '5', 'category_id' => '1']);
        // Post::create(['no' => '5', 'category_id' => '2']);
        // Post::create(['no' => '6', 'category_id' => '1']);
        // Post::create(['no' => '6', 'category_id' => '2']);
        // Post::create(['no' => '7', 'category_id' => '1']);
        // Post::create(['no' => '7', 'category_id' => '2']);
        // Post::create(['no' => '8', 'category_id' => '1']);
        // Post::create(['no' => '8', 'category_id' => '2']);
        // Post::create(['no' => '9', 'category_id' => '1']);
        // Post::create(['no' => '9', 'category_id' => '2']);
        // Post::create(['no' => '10', 'category_id' => '1']);
        // Post::create(['no' => '10', 'category_id' => '2']);

        Post::create(['no' => '1', 'division' => 'manji']);
        Post::create(['no' => '1', 'division' => 'veci']);
        Post::create(['no' => '2', 'division' => 'manji']);
        Post::create(['no' => '2', 'division' => 'veci']);
        Post::create(['no' => '3', 'division' => 'manji']);
        Post::create(['no' => '3', 'division' => 'veci']);
        Post::create(['no' => '4', 'division' => 'manji']);
        Post::create(['no' => '4', 'division' => 'veci']);
        Post::create(['no' => '5', 'division' => 'manji']);
        Post::create(['no' => '5', 'division' => 'veci']);
        Post::create(['no' => '6', 'division' => 'manji']);
        Post::create(['no' => '6', 'division' => 'veci']);
        Post::create(['no' => '7', 'division' => 'manji']);
        Post::create(['no' => '7', 'division' => 'veci']);
        Post::create(['no' => '8', 'division' => 'manji']);
        Post::create(['no' => '8', 'division' => 'veci']);
        Post::create(['no' => '9', 'division' => 'manji']);
        Post::create(['no' => '9', 'division' => 'veci']);
        Post::create(['no' => '10', 'division' => 'manji']);
        Post::create(['no' => '10', 'division' => 'veci']);
    }
}