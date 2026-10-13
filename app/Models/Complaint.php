<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    public const CATEGORIES = [
        'product' => 'Product quality / wrong item',
        'delivery' => 'Delivery problem',
        'payment' => 'Payment / refund',
        'service' => 'Customer service',
        'other' => 'Other',
    ];

    public const STATUSES = [
        'open' => ['label' => 'Open', 'color' => 'warning'],
        'in_progress' => ['label' => 'In Progress', 'color' => 'info'],
        'resolved' => ['label' => 'Resolved', 'color' => 'success'],
        'closed' => ['label' => 'Closed', 'color' => 'secondary'],
    ];

    protected $fillable = [
        'complaint_number',
        'customer_id',
        'order_id',
        'name',
        'email',
        'phone',
        'category',
        'subject',
        'message',
        'status',
        'admin_note',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => self::STATUSES[$this->status]['label'] ?? ucfirst($this->status));
    }

    protected function statusColor(): Attribute
    {
        return Attribute::get(fn () => self::STATUSES[$this->status]['color'] ?? 'secondary');
    }

    protected function categoryLabel(): Attribute
    {
        return Attribute::get(fn () => self::CATEGORIES[$this->category] ?? ucfirst($this->category));
    }
}
