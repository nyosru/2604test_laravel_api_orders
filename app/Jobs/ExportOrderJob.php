<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class ExportOrderJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public readonly int $orderId,
    ) {
        $this->onConnection('redis');
    }

    public function handle(): void
    {
        $order = Order::query()
            ->with(['customer', 'items.product'])
            ->findOrFail($this->orderId);

        Http::post(config('services.order_export.url'), [
            'order' => [
                'id' => $order->id,
                'status' => $order->status->value,
                'total_amount' => (float) $order->total_amount,
                'confirmed_at' => $order->confirmed_at?->toISOString(),
                'created_at' => $order->created_at?->toISOString(),
            ],
            'customer' => $order->customer ? [
                'id' => $order->customer->id,
                'name' => $order->customer->name,
                'email' => $order->customer->email,
            ] : null,
            'items' => $order->items->map(static fn ($item): array => [
                'product_id' => $item->product_id,
                'product_name' => $item->product?->name,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'total_price' => (float) $item->total_price,
            ])->all(),
        ])->throw();
    }
}
