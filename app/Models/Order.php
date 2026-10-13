<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUSES = [
        'pending' => ['label' => 'Pending', 'color' => 'warning', 'icon' => 'bi-hourglass-split'],
        'confirmed' => ['label' => 'Confirmed', 'color' => 'info', 'icon' => 'bi-check2-circle'],
        'processing' => ['label' => 'Processing', 'color' => 'primary', 'icon' => 'bi-gear'],
        'shipped' => ['label' => 'Shipped', 'color' => 'secondary', 'icon' => 'bi-truck'],
        'delivered' => ['label' => 'Delivered', 'color' => 'success', 'icon' => 'bi-house-check'],
        'cancelled' => ['label' => 'Cancelled', 'color' => 'danger', 'icon' => 'bi-x-circle'],
    ];

    /** Statuses after which nothing can change. */
    public const FINAL_STATUSES = ['delivered', 'cancelled'];

    public const PAYMENT_METHODS = [
        'cod' => 'Cash on Delivery',
        'credit' => '28-Day Credit',
        'sslcommerz' => 'Card / Mobile Banking (SSLCommerz)',
        'bkash' => 'bKash',
    ];

    public const PAYMENT_STATUSES = [
        'unpaid' => ['label' => 'Unpaid', 'color' => 'warning'],
        'paid' => ['label' => 'Paid', 'color' => 'success'],
        'failed' => ['label' => 'Failed', 'color' => 'danger'],
        'refunded' => ['label' => 'Refunded', 'color' => 'secondary'],
    ];

    protected $fillable = [
        'order_number',
        'customer_id',
        'status',
        'payment_method',
        'payment_status',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'shipping_area',
        'shipping_city',
        'shipping_postal_code',
        'shipping_zone',
        'subtotal',
        'discount',
        'shipping_charge',
        'total',
        'notes',
        'due_date',
        'paid_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'shipping_charge' => 'decimal:2',
            'total' => 'decimal:2',
            'due_date' => 'date',
            'paid_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest('id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest('id');
    }

    public function isOnlinePayment(): bool
    {
        return in_array($this->payment_method, ['sslcommerz', 'bkash'], true);
    }

    /** An online order that is still waiting for (or failed) payment. */
    public function canBePaidOnline(): bool
    {
        return $this->isOnlinePayment()
            && in_array($this->payment_status, ['unpaid', 'failed'], true)
            && ! $this->isFinal();
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function isFinal(): bool
    {
        return in_array($this->status, self::FINAL_STATUSES, true);
    }

    public function canBeCancelledByCustomer(): bool
    {
        return $this->status === 'pending';
    }

    public function isOverdue(): bool
    {
        return $this->payment_method === 'credit'
            && $this->payment_status !== 'paid'
            && $this->due_date?->isPast();
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => self::STATUSES[$this->status]['label'] ?? ucfirst($this->status));
    }

    protected function statusColor(): Attribute
    {
        return Attribute::get(fn () => self::STATUSES[$this->status]['color'] ?? 'secondary');
    }

    protected function paymentMethodLabel(): Attribute
    {
        return Attribute::get(fn () => self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method);
    }

    protected function paymentStatusLabel(): Attribute
    {
        return Attribute::get(fn () => self::PAYMENT_STATUSES[$this->payment_status]['label'] ?? ucfirst($this->payment_status));
    }

    protected function paymentStatusColor(): Attribute
    {
        return Attribute::get(fn () => self::PAYMENT_STATUSES[$this->payment_status]['color'] ?? 'secondary');
    }

    protected function shippingFullAddress(): Attribute
    {
        return Attribute::get(fn () => collect([$this->shipping_address, $this->shipping_area, $this->shipping_city, $this->shipping_postal_code])
            ->filter()
            ->implode(', '));
    }
}
