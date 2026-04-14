<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Jobs\ExportOrderJob;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderStatusUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_dispatches_export_job_when_order_is_confirmed(): void
    {
        Queue::fake();

        $order = Order::factory()->create([
            'status' => OrderStatus::New,
            'confirmed_at' => null,
        ]);

        $response = $this->patchJson("/api/v1/orders/{$order->id}/status", [
            'status' => OrderStatus::Confirmed->value,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.status', OrderStatus::Confirmed->value);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::Confirmed->value,
        ]);

        $this->assertNotNull($order->fresh()->confirmed_at);

        Queue::assertPushed(ExportOrderJob::class, function (ExportOrderJob $job) use ($order): bool {
            return $job->orderId === $order->id
                && $job->connection === 'redis'
                && $job->tries === 3;
        });
    }

    public function test_it_does_not_dispatch_export_job_when_status_is_not_changed_to_confirmed(): void
    {
        Queue::fake();

        $order = Order::factory()->create([
            'status' => OrderStatus::Processing,
        ]);

        $response = $this->patchJson("/api/v1/orders/{$order->id}/status", [
            'status' => OrderStatus::Shipped->value,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.status', OrderStatus::Shipped->value);

        Queue::assertNothingPushed();
    }
}
