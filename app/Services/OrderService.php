<?php

namespace App\Services;

use App\DTOs\Orders\CreateOrderData;
use App\DTOs\Orders\OrderFilterData;
use App\DTOs\Orders\UpdateOrderStatusData;
use App\Enums\OrderStatus;
use App\Http\Requests\Api\V1\ApiRequest;
use App\Jobs\ExportOrderJob;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\OrderItemRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly OrderItemRepositoryInterface $orderItemRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly ProductRepositoryInterface $productRepository,
    ) {
    }

    public function create(CreateOrderData $data): Order
    {
        return DB::transaction(function () use ($data): Order {
            $this->customerRepository->findOrFail($data->customerId);

            $products = $this->productRepository->getByIdsForUpdate(
                array_map(
                    static fn ($item): int => $item->productId,
                    $data->items,
                )
            );

            foreach ($data->items as $item) {
                /** @var Product|null $product */
                $product = $products->get($item->productId);

                if (! $product) {
                    throw (new ModelNotFoundException())->setModel(Product::class, [$item->productId]);
                }

                if ($product->stock_quantity < $item->quantity) {
                    throw ValidationException::withMessages([
                        'items' => [sprintf(
                            'Insufficient stock for product ID %d. Available: %d, requested: %d.',
                            $product->id,
                            $product->stock_quantity,
                            $item->quantity,
                        )],
                    ]);
                }
            }

            $order = $this->orderRepository->create([
                'customer_id' => $data->customerId,
                'status' => OrderStatus::New,
                'total_amount' => 0,
            ]);

            $totalAmount = 0.0;

            foreach ($data->items as $item) {
                /** @var Product $product */
                $product = $products->get($item->productId);
                $unitPrice = round((float) $product->price, 2);
                $lineTotal = round($unitPrice * $item->quantity, 2);

                $this->orderItemRepository->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $lineTotal,
                ]);

                $this->productRepository->decrementStock($product, $item->quantity);

                $totalAmount += $lineTotal;
            }

            $this->orderRepository->updateTotals($order, $totalAmount);

            return $this->findOrderOrFail($order->id, [
                'customer',
                'items.product',
            ]);
        });
    }

    public function paginate(OrderFilterData $filter): LengthAwarePaginator
    {
        return $this->orderRepository->paginate($filter, [
            'customer',
            'items.product',
        ]);
    }

    public function getById(int $orderId): Order
    {
        return $this->findOrderOrFail($orderId, [
            'customer',
            'items.product',
        ]);
    }

    public function updateStatus(UpdateOrderStatusData $data): Order
    {
        $order = DB::transaction(function () use ($data): Order {
            $order = $this->findOrderOrFail($data->orderId, [
                'customer',
                'items.product',
            ]);

            $wasConfirmed = $order->status === OrderStatus::Confirmed;
            $attributes = [];

            if (! $wasConfirmed && $data->status === OrderStatus::Confirmed) {
                $attributes['confirmed_at'] = now();
            }

            $updatedOrder = $this->orderRepository->updateStatus($order, $data->status, $attributes);

            if (! $wasConfirmed && $data->status === OrderStatus::Confirmed) {
                ExportOrderJob::dispatch($updatedOrder->id)->afterCommit();
            }

            return $updatedOrder;
        });

        return $this->findOrderOrFail($order->id, [
            'customer',
            'items.product',
        ]);
    }

    /**
     * @param  array<int, string>  $with
     */
    private function findOrderOrFail(int $orderId, array $with = []): Order
    {
        try {
            return $this->orderRepository->findOrFail($orderId, $with);
        } catch (ModelNotFoundException $exception) {
            ApiRequest::notFound('Not found');
        }
    }
}
