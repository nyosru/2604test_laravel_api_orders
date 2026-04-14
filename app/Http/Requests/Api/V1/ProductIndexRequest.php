<?php

namespace App\Http\Requests\Api\V1;

use App\DTOs\Products\ProductFilterData;

class ProductIndexRequest extends ApiRequest
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
            'category' => ['nullable', 'string', 'max:255'],
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toDto(): ProductFilterData
    {
        /** @var array{category?:string|null,search?:string|null,per_page?:int|null} $validated */
        $validated = $this->validated();

        return ProductFilterData::fromArray($validated);
    }
}
