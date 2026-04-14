<?php

namespace App\Repositories\Contracts;

use App\DTOs\Products\ProductFilterData;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;

    public function findOrFail(int $id): Product;

    /**
     * @param  list<int>  $ids
     * @return Collection<int, Product>
     */
    public function getByIds(array $ids): Collection;

    /**
     * @param  list<int>  $ids
     * @return SupportCollection<int, Product>
     */
    public function getByIdsForUpdate(array $ids): SupportCollection;

    public function paginate(ProductFilterData $filter): LengthAwarePaginator;

    public function decrementStock(Product $product, int $quantity): void;
}
