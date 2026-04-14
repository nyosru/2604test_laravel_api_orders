<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
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
            'customer_id' => Customer::factory(),
            'status' => OrderStatus::New,
            'total_amount' => 0,
            'confirmed_at' => null,
            'shipped_at' => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => [
            'status' => OrderStatus::Confirmed,
            'confirmed_at' => fake()->dateTimeBetween('-14 days', 'now'),
        ]);
    }

    public function processing(): static
    {
        return $this->state(function () {
            $confirmedAt = fake()->dateTimeBetween('-14 days', '-3 days');

            return [
                'status' => OrderStatus::Processing,
                'confirmed_at' => $confirmedAt,
                'shipped_at' => null,
            ];
        });
    }

    public function shipped(): static
    {
        return $this->state(function () {
            $confirmedAt = fake()->dateTimeBetween('-20 days', '-7 days');

            return [
                'status' => OrderStatus::Shipped,
                'confirmed_at' => $confirmedAt,
                'shipped_at' => fake()->dateTimeBetween($confirmedAt, '-1 day'),
            ];
        });
    }

    public function completed(): static
    {
        return $this->state(function () {
            $confirmedAt = fake()->dateTimeBetween('-30 days', '-10 days');

            return [
                'status' => OrderStatus::Completed,
                'confirmed_at' => $confirmedAt,
                'shipped_at' => fake()->dateTimeBetween($confirmedAt, '-2 days'),
            ];
        });
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => OrderStatus::Cancelled,
            'confirmed_at' => fake()->boolean(40) ? fake()->dateTimeBetween('-7 days', '-1 day') : null,
            'shipped_at' => null,
        ]);
    }
}
