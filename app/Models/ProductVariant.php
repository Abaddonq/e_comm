<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'compare_at_price',
        'attributes',
        'weight',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'attributes' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the product that owns the variant.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the stock movements for the variant.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get the cart items for the variant.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the order items for the variant.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the current stock level for the variant.
     */
    public function getCurrentStock(): int
    {
        return $this->stockMovements()->sum('quantity_change');
    }

    /**
     * Check if the variant is in stock.
     */
    public function isInStock(int $quantity = 1): bool
    {
        return $this->getCurrentStock() >= $quantity;
    }

    /**
     * Get the display name for the variant.
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->product->title;
        if ($this->attributes && is_array($this->attributes)) {
            $attrs = collect($this->attributes)
                ->map(fn($v, $k) => "$k: $v")
                ->join(', ');
            $name .= " ($attrs)";
        }
        return $name;
    }
}
