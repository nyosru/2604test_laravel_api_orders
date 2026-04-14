<?php

namespace App\Services;

use App\DTOs\Products\ProductFilterData;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function paginate(ProductFilterData $filter): LengthAwarePaginator
    {
        return $this->productRepository->paginate($filter);
    }
}
