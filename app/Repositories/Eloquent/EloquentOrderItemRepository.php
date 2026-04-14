<?php

namespace App\Repositories\Eloquent;

use App\Models\OrderItem;
use App\Repositories\Contracts\OrderItemRepositoryInterface;

class EloquentOrderItemRepository implements OrderItemRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): OrderItem
    {
        return OrderItem::query()->create($attributes);
    }
}
