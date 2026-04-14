<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'status',
        'total_amount',
        'confirmed_at',
        'shipped_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'total_amount' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'shipped_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeStatus(Builder $query, OrderStatus|string|null $status): Builder
    {
        $statusValue = $status instanceof OrderStatus ? $status->value : $status;

        return $query->when($statusValue, fn (Builder $builder) => $builder->where('status', $statusValue));
    }

    public function scopeCustomer(Builder $query, ?int $customerId): Builder
    {
        return $query->when($customerId, fn (Builder $builder) => $builder->where('customer_id', $customerId));
    }

    public function scopeCreatedBetween(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query
            ->when($from, fn (Builder $builder) => $builder->whereDate('created_at', '>=', $from))
            ->when($to, fn (Builder $builder) => $builder->whereDate('created_at', '<=', $to));
    }
}
