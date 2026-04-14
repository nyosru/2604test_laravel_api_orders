<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Jobs\ExportOrderJob;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExportOrderJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_posts_order_payload_to_configured_url(): void
    {
        $fake_uri = 'https://httpbin.org/';

        Http::fake([
            $fake_uri => Http::response(['ok' => true], 200),
        ]);

        config()->set('services.order_export.url', $fake_uri);

        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::Confirmed,
            'confirmed_at' => now(),
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        $job = new ExportOrderJob($order->id);
        $job->handle();

        Http::assertSent(function (Request $request) use ($order, $customer, $product, $fake_uri): bool {
            $data = $request->data();

            return $request->method() === 'POST'
                && $request->url() == $fake_uri
                && data_get($data, 'order.id') === $order->id
                && data_get($data, 'order.status') === OrderStatus::Confirmed->value
                && data_get($data, 'customer.id') === $customer->id
                && data_get($data, 'items.0.product_id') === $product->id;
        });
    }

    public function test_it_throws_exception_for_unsuccessful_response(): void
    {
        Http::fake([
            '*' => Http::response(['error' => 'failed'], 500),
        ]);

        $order = Order::factory()->create([
            'status' => OrderStatus::Confirmed,
            'confirmed_at' => now(),
        ]);

        $job = new ExportOrderJob($order->id);

        $this->assertSame(3, $job->tries);
        $this->expectException(RequestException::class);

        $job->handle();
    }
}
