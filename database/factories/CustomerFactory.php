<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'contact_id' => Contact::factory(),
            'customer_code' => 'CUS-' . fake()->unique()->numberBetween(1, 999999),
            'status' => 'active',
            'became_customer_at' => now(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
