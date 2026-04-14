<?php

namespace App\Http\Requests\Api\V1;

use App\DTOs\Orders\OrderFilterData;
use App\Enums\OrderStatus;
use Illuminate\Validation\Rule;

class OrderIndexRequest extends ApiRequest
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
            'status' => ['nullable', 'string', Rule::in(OrderStatus::values())],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toDto(): OrderFilterData
    {
        /** @var array{status?:string|null,customer_id?:int|null,date_from?:string|null,date_to?:string|null,per_page?:int|null} $validated */
        $validated = $this->validated();

        return OrderFilterData::fromArray($validated);
    }
}
