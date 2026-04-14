<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Orders\OrderFilterData;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Order
    {
        return Order::query()->create($attributes);
    }

    public function findById(int $id, array $with = []): ?Order
    {
        return Order::query()
            ->with($with)
            ->find($id);
    }

    public function findOrFail(int $id, array $with = []): Order
    {
        return Order::query()
            ->with($with)
            ->findOrFail($id);
    }

    public function paginate(OrderFilterData $filter, array $with = []): LengthAwarePaginator
    {
        return Order::query()
            ->with($with)
            ->status($filter->status)
            ->customer($filter->customerId)
            ->createdBetween($filter->dateFrom, $filter->dateTo)
            ->latest()
            ->paginate($filter->perPage);
    }

    public function updateStatus(Order $order, OrderStatus $status, array $attributes = []): Order
    {
        $order->forceFill([
            'status' => $status,
            ...$attributes,
        ])->save();

        return $order->refresh();
    }

    public function updateTotals(Order $order, float $totalAmount): Order
    {
        $order->forceFill([
            'total_amount' => round($totalAmount, 2),
        ])->save();

        return $order->refresh();
    }
}
