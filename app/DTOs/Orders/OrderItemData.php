<?php

namespace App\DTOs\Orders;

final readonly class OrderItemData
{
    public function __construct(
        public int $productId,
        public int $quantity,
    ) {}

    /**
     * @param  array{product_id:int,quantity:int}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            productId: $data['product_id'],
            quantity: $data['quantity'],
        );
    }

    /**
     * @return array{product_id:int,quantity:int}
     */
    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'quantity' => $this->quantity,
        ];
    }
}
