<?php

namespace App\Repositories\Contracts;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

interface CustomerRepositoryInterface
{
    public function findById(int $id): ?Customer;

    public function findOrFail(int $id): Customer;

    /**
     * @return Collection<int, Customer>
     */
    public function all(): Collection;
}
