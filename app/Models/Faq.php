<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    public const CATEGORIES = [
        'general' => ['label' => 'General', 'icon' => 'bi-info-circle'],
        'shipping' => ['label' => 'Shipping & Returns', 'icon' => 'bi-truck'],
        'payment' => ['label' => 'Payment', 'icon' => 'bi-credit-card'],
        'account' => ['label' => 'Account & Orders', 'icon' => 'bi-person'],
    ];

    protected $fillable = [
        'category',
        'question',
        'answer',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category]['label'] ?? ucfirst($this->category);
    }
}
