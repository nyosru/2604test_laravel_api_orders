<?php

namespace App\Http\Requests\Api\V1;

use App\DTOs\Orders\UpdateOrderStatusData;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class OrderUpdateStatusRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(OrderStatus::values())],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $orderId = (int) $this->route('order');
            $status = $this->input('status');

            if (! $orderId || ! is_string($status)) {
                return;
            }

            $nextStatus = OrderStatus::tryFrom($status);

            if (! $nextStatus) {
                return;
            }

            /** @var Order|null $order */
            $order = Order::query()->find($orderId);

            if (! $order) {
                return;
            }

            if (! $this->isAllowedTransition($order->status, $nextStatus)) {
                $validator->errors()->add(
                    'status',
                    sprintf(
                        'Status transition from "%s" to "%s" is not allowed.',
                        $order->status->value,
                        $nextStatus->value,
                    )
                );
            }
        });
    }

    public function toDto(int $orderId): UpdateOrderStatusData
    {
        /** @var array{status:string} $validated */
        $validated = $this->validated();

        return UpdateOrderStatusData::fromArray($orderId, $validated);
    }

    private function isAllowedTransition(OrderStatus $currentStatus, OrderStatus $nextStatus): bool
    {
        return match ($currentStatus) {
            OrderStatus::New => in_array($nextStatus, [OrderStatus::Confirmed, OrderStatus::Cancelled], true),
            OrderStatus::Confirmed => in_array($nextStatus, [OrderStatus::Processing, OrderStatus::Cancelled], true),
            OrderStatus::Processing => $nextStatus === OrderStatus::Shipped,
            OrderStatus::Shipped => $nextStatus === OrderStatus::Completed,
            OrderStatus::Completed, OrderStatus::Cancelled => false,
        };
    }
}
