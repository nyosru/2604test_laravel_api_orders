<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::query()->get();
        $products = Product::query()->where('stock_quantity', '>', 3)->get();

        if ($customers->isEmpty() || $products->count() < 3) {
            return;
        }

        $statuses = [
            OrderStatus::New,
            OrderStatus::Confirmed,
            OrderStatus::Processing,
            OrderStatus::Shipped,
            OrderStatus::Completed,
            OrderStatus::Cancelled,
        ];

        foreach (range(1, 12) as $_) {
            $status = fake()->randomElement($statuses);
            $order = Order::factory()
                ->for($customers->random())
                ->state($this->stateForStatus($status))
                ->create();

            $selectedProducts = $this->pickProducts($products);
            $total = 0;

            foreach ($selectedProducts as $product) {
                $quantity = fake()->numberBetween(1, min(3, max(1, $product->stock_quantity)));
                $lineTotal = round($quantity * (float) $product->price, 2);

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total_price' => $lineTotal,
                ]);

                if ($status !== OrderStatus::Cancelled) {
                    $product->decrement('stock_quantity', $quantity);
                    $product->refresh();
                }

                $total += $lineTotal;
            }

            $order->update([
                'total_amount' => round($total, 2),
            ]);
        }
    }

    /**
     * @param  Collection<int, Product>  $products
     * @return Collection<int, Product>
     */
    private function pickProducts(Collection $products): Collection
    {
        return $products
            ->filter(fn (Product $product) => $product->stock_quantity > 0)
            ->shuffle()
            ->take(fake()->numberBetween(1, 4))
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    private function stateForStatus(OrderStatus $status): array
    {
        return match ($status) {
            OrderStatus::New => [
                'status' => OrderStatus::New,
                'confirmed_at' => null,
                'shipped_at' => null,
            ],
            OrderStatus::Confirmed => [
                'status' => OrderStatus::Confirmed,
                'confirmed_at' => fake()->dateTimeBetween('-10 days', 'now'),
                'shipped_at' => null,
            ],
            OrderStatus::Processing => [
                'status' => OrderStatus::Processing,
                'confirmed_at' => fake()->dateTimeBetween('-14 days', '-3 days'),
                'shipped_at' => null,
            ],
            OrderStatus::Shipped => $this->shippedState(OrderStatus::Shipped),
            OrderStatus::Completed => $this->shippedState(OrderStatus::Completed),
            OrderStatus::Cancelled => [
                'status' => OrderStatus::Cancelled,
                'confirmed_at' => fake()->boolean()
                    ? fake()->dateTimeBetween('-7 days', '-1 day')
                    : null,
                'shipped_at' => null,
            ],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function shippedState(OrderStatus $status): array
    {
        $confirmedAt = fake()->dateTimeBetween('-21 days', '-5 days');

        return [
            'status' => $status,
            'confirmed_at' => $confirmedAt,
            'shipped_at' => fake()->dateTimeBetween($confirmedAt, 'now'),
        ];
    }
}
