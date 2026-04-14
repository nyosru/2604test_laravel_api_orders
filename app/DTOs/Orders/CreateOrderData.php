<?php

namespace App\DTOs\Orders;

use InvalidArgumentException;

final readonly class CreateOrderData
{
    /**
     * @param  list<OrderItemData>  $items
     */
    public function __construct(
        public int $customerId,
        public array $items,
    ) {
        if ($items === []) {
            throw new InvalidArgumentException('Order must contain at least one item.');
        }
    }

    /**
     * @param  array{customer_id:int,items:list<array{product_id:int,quantity:int}>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            customerId: $data['customer_id'],
            items: array_map(
                static fn (array $item): OrderItemData => OrderItemData::fromArray($item),
                $data['items'],
            ),
        );
    }
}
