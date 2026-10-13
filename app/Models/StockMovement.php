<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    public const TYPE_IN = 'in';
    public const TYPE_OUT = 'out';

    public const SOURCES = [
        'initial' => 'Opening stock',
        'manual' => 'Manual adjustment',
        'order' => 'Order',
        'order_cancel' => 'Order cancelled',
        'myob' => 'MYOB sync',
    ];

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'source',
        'quantity',
        'before_qty',
        'after_qty',
        'note',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sourceLabel(): string
    {
        return self::SOURCES[$this->source] ?? ucfirst($this->source);
    }
}
