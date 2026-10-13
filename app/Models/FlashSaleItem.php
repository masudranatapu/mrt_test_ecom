<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSaleItem extends Model
{
    protected $fillable = [
        'flash_sale_id',
        'product_id',
        'price',
        'quantity_limit',
        'sold',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity_limit' => 'integer',
            'sold' => 'integer',
        ];
    }

    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isSoldOut(): bool
    {
        return $this->quantity_limit !== null && $this->sold >= $this->quantity_limit;
    }

    public function soldPercent(): int
    {
        return $this->quantity_limit ? (int) min(100, round($this->sold / $this->quantity_limit * 100)) : 0;
    }
}
