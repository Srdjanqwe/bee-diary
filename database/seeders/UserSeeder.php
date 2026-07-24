<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $admin->roles()->attach(Role::where('name', 'Administrator')->value('id'));

        $guest = User::factory()->create([
            'name' => 'Guest User',
            'email' => 'guest@example.com',
        ]);
        $guest->roles()->attach(Role::where('name', 'Guest')->value('id'));
    }
}
