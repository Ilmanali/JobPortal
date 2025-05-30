<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\JobType;
use App\Models\Job;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // Category::factory(5)->create();
        // Job::factory(25)->create();
        // User::factory()->count(3)->create();

        // Then create jobs
        // Job::factory()->count(25)->create();
    }
}
