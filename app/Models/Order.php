<?php

namespace App\Models;

use App\Support\OrderStatusMapper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'address_id',
        'order_number',
        'status',
        'fulfillment_status',
        'payment_status',
        'return_status',
        'status_updated_at',
        'payment_method',
        'shipping_name',
        'shipping_phone',
        'shipping_address_line1',
        'shipping_address_line2',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'subtotal',
        'shipping_cost',
        'tax',
        'total',
        'notes',
        'paid_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'status_updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the address associated with the order.
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payment for the order.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get the shipment for the order.
     */
    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    /**
     * Scope a query to only include paid orders.
     */
    public function scopePaid($query)
    {
        return $query->where(function ($innerQuery) {
            $innerQuery
                ->where('payment_status', OrderStatusMapper::PAYMENT_PAID)
                ->orWhere('status', 'paid')
                ->orWhere('status', 'processing')
                ->orWhere('status', 'shipped')
                ->orWhere('status', 'delivered');
        });
    }

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending($query)
    {
        return $query->where(function ($innerQuery) {
            $innerQuery
                ->where('fulfillment_status', OrderStatusMapper::FULFILLMENT_PENDING)
                ->orWhere(function ($legacyQuery) {
                    $legacyQuery->whereNull('fulfillment_status')->where('status', 'pending');
                });
        });
    }

    /**
     * Check if the order is paid.
     */
    public function isPaid(): bool
    {
        return in_array($this->effective_payment_status, [
            OrderStatusMapper::PAYMENT_PAID,
            OrderStatusMapper::PAYMENT_PARTIALLY_REFUNDED,
            OrderStatusMapper::PAYMENT_REFUNDED,
        ], true);
    }

    /**
     * Check if the order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->effective_fulfillment_status, [
            OrderStatusMapper::FULFILLMENT_PENDING,
            OrderStatusMapper::FULFILLMENT_PROCESSING,
            OrderStatusMapper::FULFILLMENT_PACKED,
        ], true);
    }

    public function getEffectiveFulfillmentStatusAttribute(): string
    {
        if (!empty($this->fulfillment_status)) {
            return $this->fulfillment_status;
        }

        return OrderStatusMapper::mapLegacyStatus((string) $this->status)['fulfillment_status'];
    }

    public function getEffectivePaymentStatusAttribute(): string
    {
        if (!empty($this->payment_status)) {
            return $this->payment_status;
        }

        return OrderStatusMapper::mapLegacyStatus((string) $this->status)['payment_status'];
    }

    public function getEffectiveReturnStatusAttribute(): string
    {
        if (!empty($this->return_status)) {
            return $this->return_status;
        }

        return OrderStatusMapper::mapLegacyStatus((string) $this->status)['return_status'];
    }

    public function getCustomerStatusLabelAttribute(): string
    {
        return OrderStatusMapper::customerStatusLabel($this->effective_fulfillment_status);
    }

    public function getInternalStatusLabelAttribute(): string
    {
        $labels = OrderStatusMapper::adminStatusOptions();

        return $labels[$this->effective_fulfillment_status] ?? ucfirst(str_replace('_', ' ', $this->effective_fulfillment_status));
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return OrderStatusMapper::badgeClass($this->effective_fulfillment_status);
    }

    /**
     * Get the full shipping address as a single string.
     */
    public function getShippingAddressAttribute(): string
    {
        $parts = array_filter([
            $this->shipping_address_line1,
            $this->shipping_address_line2,
            $this->shipping_city,
            $this->shipping_state,
            $this->shipping_postal_code,
            $this->shipping_country,
        ]);

        return implode(', ', $parts);
    }
}
