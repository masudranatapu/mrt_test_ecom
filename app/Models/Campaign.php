<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Campaign extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'description',
        'banner',
        'discount_percent',
        'starts_at',
        'ends_at',
        'is_featured',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'discount_percent' => 'integer',
            'is_featured' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function scopeRunning(Builder $query): Builder
    {
        return $query->where('status', true)->where('starts_at', '<=', now())->where('ends_at', '>', now());
    }

    /** Running or not started yet (what the storefront may show). */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('status', true)->where('ends_at', '>', now());
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

    /** The moment the storefront countdown runs to. */
    public function countdownTarget(): \Illuminate\Support\Carbon
    {
        return $this->starts_at->isFuture() ? $this->starts_at : $this->ends_at;
    }

    protected function bannerUrl(): Attribute
    {
        return Attribute::get(fn () => $this->banner ? storage_url($this->banner) : null);
    }
}
