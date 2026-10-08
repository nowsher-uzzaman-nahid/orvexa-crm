<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'company_name' => fake()->company(),
            'job_title' => fake()->jobTitle(),

            'lead_source_id' => LeadSource::factory(),
            'lead_status_id' => LeadStatus::factory(),
            'assigned_to' => User::factory(),

            'estimated_value' => fake()->randomFloat(2, 1000, 100000),
            'notes' => fake()->optional()->sentence(),

            'last_contacted_at' => null,
            'converted_at' => null,
        ];
    }
}
