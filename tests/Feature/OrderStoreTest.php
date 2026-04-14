<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_order_and_decrements_stock_atomically(): void
    {
        $customer = Customer::factory()->create();
        $firstProduct = Product::factory()->create([
            'price' => 100.50,
            'stock_quantity' => 10,
        ]);
        $secondProduct = Product::factory()->create([
            'price' => 25.00,
            'stock_quantity' => 5,
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $firstProduct->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $secondProduct->id,
                    'quantity' => 3,
                ],
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', OrderStatus::New->value)
            ->assertJsonPath('data.total_amount', 276)
            ->assertJsonPath('data.customer.id', $customer->id)
            ->assertJsonCount(2, 'data.items');

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'status' => OrderStatus::New->value,
            'total_amount' => '276.00',
        ]);

        $order = Order::query()->firstOrFail();

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $firstProduct->id,
            'quantity' => 2,
            'unit_price' => '100.50',
            'total_price' => '201.00',
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $secondProduct->id,
            'quantity' => 3,
            'unit_price' => '25.00',
            'total_price' => '75.00',
        ]);

        $this->assertSame(8, $firstProduct->fresh()->stock_quantity);
        $this->assertSame(2, $secondProduct->fresh()->stock_quantity);
    }

    public function test_it_returns_validation_error_and_keeps_data_unchanged_when_stock_is_not_enough(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create([
            'price' => 49.99,
            'stock_quantity' => 1,
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('items');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertSame(1, $product->fresh()->stock_quantity);
    }
}
