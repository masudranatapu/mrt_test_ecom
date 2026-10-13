<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlashSale extends Model
{
    protected $fillable = [
        'title',
        'starts_at',
        'ends_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'status' => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(FlashSaleItem::class);
    }

    public function scopeRunning(Builder $query): Builder
    {
        return $query->where('status', true)->where('starts_at', '<=', now())->where('ends_at', '>', now());
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('status', true)->where('starts_at', '>', now());
    }

    public function isRunning(): bool
    {
        return $this->status && $this->starts_at->isPast() && $this->ends_at->isFuture();
    }

    public function state(): string
    {
        return match (true) {
            ! $this->status => 'disabled',
            $this->starts_at->isFuture() => 'upcoming',
            $this->ends_at->isPast() => 'ended',
            default => 'running',
        };
    }
}
