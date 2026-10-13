<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Services\PromotionService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    /** Stock above this number is shown as "50+" on the storefront. */
    public const STOCK_DISPLAY_LIMIT = 50;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'price',
        'sale_price',
        'unit',
        'stock_qty',
        'sold_qty',
        'low_stock_alert',
        'best_before_date',
        'thumbnail',
        'is_featured',
        'status',
        'myob_uid',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock_qty' => 'integer',
            'sold_qty' => 'integer',
            'low_stock_alert' => 'integer',
            'best_before_date' => 'date',
            'is_featured' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock_qty', '>', 0);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    /**
     * Price the customer actually pays: the lower of the regular / sale price
     * and any running flash sale or campaign price.
     */
    protected function finalPrice(): Attribute
    {
        return Attribute::get(fn () => (string) ($this->promotion()['price'] ?? $this->regularSellingPrice()));
    }

    protected function discountPercent(): Attribute
    {
        return Attribute::get(fn () => $this->hasDiscount()
            ? (int) round(100 - ((float) $this->final_price / (float) $this->price * 100))
            : 0);
    }

    /** Regular or sale price, without promotions. */
    public function regularSellingPrice(): float
    {
        return $this->hasSalePrice() ? (float) $this->sale_price : (float) $this->price;
    }

    /**
     * Running flash sale / campaign offer for this product, if any.
     *
     * @return array{type: string, price: float, ends_at: \Illuminate\Support\Carbon, label: string}|null
     */
    public function promotion(): ?array
    {
        return app(PromotionService::class)->bestFor($this);
    }

    /** "50+" when stock is above the display limit, otherwise the exact number. */
    protected function stockLabel(): Attribute
    {
        return Attribute::get(fn () => $this->stock_qty > self::STOCK_DISPLAY_LIMIT
            ? self::STOCK_DISPLAY_LIMIT.'+'
            : (string) $this->stock_qty);
    }

    protected function isLowStock(): Attribute
    {
        return Attribute::get(fn () => $this->stock_qty > 0 && $this->stock_qty <= $this->low_stock_alert);
    }

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::get(fn () => $this->thumbnail
            ? storage_url($this->thumbnail)
            : default_image());
    }

    /** Customer pays less than the regular price (sale price or promotion). */
    public function hasDiscount(): bool
    {
        return (float) $this->final_price < (float) $this->price;
    }

    /** A sale price set on the product itself (admin side). */
    public function hasSalePrice(): bool
    {
        return $this->sale_price !== null && (float) $this->sale_price > 0 && (float) $this->sale_price < (float) $this->price;
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class);
    }
}
