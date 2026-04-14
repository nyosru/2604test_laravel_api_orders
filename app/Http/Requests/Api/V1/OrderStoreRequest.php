<?php

namespace App\Http\Requests\Api\V1;

use App\DTOs\Orders\CreateOrderData;

class OrderStoreRequest extends ApiRequest
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
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function toDto(): CreateOrderData
    {
        /** @var array{customer_id:int,items:list<array{product_id:int,quantity:int}>} $validated */
        $validated = $this->validated();

        return CreateOrderData::fromArray($validated);
    }
}
