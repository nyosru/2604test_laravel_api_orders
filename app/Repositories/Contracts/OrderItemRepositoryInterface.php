<?php

namespace App\Repositories\Contracts;

use App\Models\OrderItem;

interface OrderItemRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): OrderItem;
}
