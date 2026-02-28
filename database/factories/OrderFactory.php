<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-' . strtoupper(fake()->bothify('????######')),
            'status' => 'pending',
            'payment_method' => fake()->randomElement(['iyzico', 'stripe']),
            'shipping_name' => fake()->name(),
            'shipping_phone' => fake()->phoneNumber(),
            'shipping_address_line1' => fake()->streetAddress(),
            'shipping_address_line2' => fake()->secondaryAddress(),
            'shipping_city' => fake()->city(),
            'shipping_state' => fake()->state(),
            'shipping_postal_code' => fake()->postcode(),
            'shipping_country' => fake()->countryCode(),
            'subtotal' => fake()->randomFloat(2, 100, 1000),
            'shipping_cost' => fake()->randomFloat(2, 10, 50),
            'tax' => fake()->randomFloat(2, 10, 100),
            'total' => fake()->randomFloat(2, 200, 1500),
            'notes' => null,
            'paid_at' => null,
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => fake()->sentence(),
        ]);
    }
}
