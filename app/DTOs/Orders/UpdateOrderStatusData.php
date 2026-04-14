<?php

namespace App\DTOs\Orders;

use App\Enums\OrderStatus;

final readonly class UpdateOrderStatusData
{
    public function __construct(
        public int $orderId,
        public OrderStatus $status,
    ) {
    }

    /**
     * @param  array{status:string}  $data
     */
    public static function fromArray(int $orderId, array $data): self
    {
        return new self(
            orderId: $orderId,
            status: OrderStatus::from($data['status']),
        );
    }
}
