<?php

namespace App\Http\Requests\Api\V1;

use App\DTOs\Orders\UpdateOrderStatusData;
use App\Enums\OrderStatus;
use Illuminate\Validation\Rule;

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

    public function toDto(int $orderId): UpdateOrderStatusData
    {
        /** @var array{status:string} $validated */
        $validated = $this->validated();

        return UpdateOrderStatusData::fromArray($orderId, $validated);
    }
}
