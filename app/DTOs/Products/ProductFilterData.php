<?php

namespace App\DTOs\Products;

final readonly class ProductFilterData
{
    public function __construct(
        public ?string $category = null,
        public ?string $search = null,
        public int $perPage = 15,
    ) {
    }

    /**
     * @param  array{category?:string|null,search?:string|null,per_page?:int|null}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            category: $data['category'] ?? null,
            search: $data['search'] ?? null,
            perPage: $data['per_page'] ?? 15,
        );
    }
}
