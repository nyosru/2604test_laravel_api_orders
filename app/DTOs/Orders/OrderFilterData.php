<?php

namespace App\DTOs\Orders;

use App\Enums\OrderStatus;

final readonly class OrderFilterData
{
    public function __construct(
        public ?OrderStatus $status = null,
        public ?int $customerId = null,
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public int $perPage = 15,
    ) {
    }

    /**
     * @param  array{status?:string|null,customer_id?:int|null,date_from?:string|null,date_to?:string|null,per_page?:int|null}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            status: isset($data['status']) && $data['status'] !== null
                ? OrderStatus::from($data['status'])
                : null,
            customerId: $data['customer_id'] ?? null,
            dateFrom: $data['date_from'] ?? null,
            dateTo: $data['date_to'] ?? null,
            perPage: $data['per_page'] ?? 15,
        );
    }
}
