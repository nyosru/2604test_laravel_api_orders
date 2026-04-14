<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function findById(int $id): ?Customer
    {
        return Customer::query()->find($id);
    }

    public function findOrFail(int $id): Customer
    {
        return Customer::query()->findOrFail($id);
    }

    /**
     * @return Collection<int, Customer>
     */
    public function all(): Collection
    {
        return Customer::query()
            ->orderBy('name')
            ->get();
    }
}
