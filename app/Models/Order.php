<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    public const SERVICE_PENDING = 'pending';

    public const SERVICE_PREPARING = 'preparing';

    public const SERVICE_SERVED = 'served';

    public const PAYMENT_UNPAID = 'unpaid';

    public const PAYMENT_PAID = 'paid';

    protected $fillable = [
        'reference',
        'table_number',
        'menu_item_id',
        'worker_id',
        'served_by',
        'items_count',
        'total',
        'status',
        'service_status',
        'payment_status',
        'payment_method',
        'served_at',
        'paid_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'items_count' => 'integer',
            'total' => 'integer',
            'served_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<MenuItem, $this>
     */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * @return BelongsTo<Worker, $this>
     */
    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(User::class, 'served_by');
    }

    public function isPending(): bool
    {
        return $this->service_status === self::SERVICE_PENDING;
    }

    public function isServed(): bool
    {
        return $this->service_status === self::SERVICE_SERVED;
    }

    public function isUnpaid(): bool
    {
        return $this->payment_status === self::PAYMENT_UNPAID;
    }

    public function markPreparing(): void
    {
        $this->update([
            'service_status' => self::SERVICE_PREPARING,
            'status' => 'Preparing',
        ]);
    }

    public function markServed(?User $waiter = null): void
    {
        $this->update([
            'service_status' => self::SERVICE_SERVED,
            'status' => 'Served',
            'served_by' => $waiter?->id,
            'served_at' => now(),
        ]);
    }

    public function markPaid(string $method, ?User $waiter = null): void
    {
        $this->update([
            'payment_status' => self::PAYMENT_PAID,
            'payment_method' => $method,
            'paid_at' => now(),
            'status' => 'Completed',
            'service_status' => self::SERVICE_SERVED,
            'served_by' => $this->served_by ?? $waiter?->id,
            'served_at' => $this->served_at ?? now(),
        ]);
    }
}
