<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Products\ProductFilterData;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function findById(int $id): ?Product
    {
        return Product::query()->find($id);
    }

    public function findOrFail(int $id): Product
    {
        return Product::query()->findOrFail($id);
    }

    /**
     * @param  list<int>  $ids
     * @return Collection<int, Product>
     */
    public function getByIds(array $ids): Collection
    {
        return Product::query()
            ->whereIn('id', $ids)
            ->orderBy('name')
            ->get();
    }

    /**
     * @param  list<int>  $ids
     * @return SupportCollection<int, Product>
     */
    public function getByIdsForUpdate(array $ids): SupportCollection
    {
        return Product::query()
            ->whereIn('id', $ids)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }

    public function paginate(ProductFilterData $filter): LengthAwarePaginator
    {
        return Product::query()
            ->category($filter->category)
            ->search($filter->search)
            ->orderBy('name')
            ->paginate($filter->perPage);
    }

    public function decrementStock(Product $product, int $quantity): void
    {
        $product->decrement('stock_quantity', $quantity);
    }
}
