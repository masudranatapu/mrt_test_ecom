<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const GATEWAYS = [
        'sslcommerz' => 'SSLCommerz',
        'bkash' => 'bKash',
    ];

    protected $fillable = [
        'order_id',
        'gateway',
        'transaction_id',
        'gateway_reference',
        'gateway_transaction_id',
        'amount',
        'currency',
        'status',
        'payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payload' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isFinished(): bool
    {
        return $this->status !== 'initiated';
    }
}
