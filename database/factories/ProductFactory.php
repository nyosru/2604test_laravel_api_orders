<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @var array<int, string>
     */
    private const CATEGORIES = [
        'engine',
        'brakes',
        'suspension',
        'filters',
        'electronics',
        'transmission',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(asText: true);

        return [
            'name' => Str::title($name),
            'sku' => strtoupper(fake()->unique()->bothify('PRD-#####')),
            'price' => fake()->randomFloat(2, 10, 1500),
            'stock_quantity' => fake()->numberBetween(5, 150),
            'category' => fake()->randomElement(self::CATEGORIES),
        ];
    }
}
