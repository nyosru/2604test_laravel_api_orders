<?php

namespace App\Repositories\Contracts;

use App\DTOs\Orders\OrderFilterData;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Order;

    public function findById(int $id, array $with = []): ?Order;

    public function findOrFail(int $id, array $with = []): Order;

    public function paginate(OrderFilterData $filter, array $with = []): LengthAwarePaginator;

    public function updateStatus(Order $order, OrderStatus $status, array $attributes = []): Order;

    public function updateTotals(Order $order, float $totalAmount): Order;
}
