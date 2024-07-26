<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure that there are users in the database
        $userIds = User::pluck('id')->toArray();

        return [
            'title' => fake()->jobTitle,
            'user_id' => fake()->randomElement($userIds),
            'job_type_id' => rand(1, 5),
            'category_id' => rand(1, 5),
            'vacancy' => rand(1, 5),
            'location' => fake()->city,
            'description' => fake()->text,
            'experience' => rand(1, 10),
            'company_name' => fake()->company,
        ];
    }
}
