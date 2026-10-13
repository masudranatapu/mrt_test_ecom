<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Every stock change goes through here so the history stays complete.
 */
class StockService
{
    /**
     * @param  string  $type  StockMovement::TYPE_IN | StockMovement::TYPE_OUT
     */
    public function adjust(
        Product $product,
        string $type,
        int $quantity,
        string $source = 'manual',
        ?string $note = null,
        ?int $adminId = null,
    ): StockMovement {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be greater than zero.');
        }

        return DB::transaction(function () use ($product, $type, $quantity, $source, $note, $adminId) {
            // Lock the row so two adjustments at once cannot overwrite each other
            // withTrashed: stock is still returned for a soft-deleted product when an order is cancelled
            $product = Product::withTrashed()->whereKey($product->id)->lockForUpdate()->firstOrFail();

            $before = $product->stock_qty;

            if ($type === StockMovement::TYPE_OUT && $quantity > $before) {
                throw new InvalidArgumentException("Only {$before} {$product->unit} in stock.");
            }

            $after = $type === StockMovement::TYPE_IN ? $before + $quantity : $before - $quantity;

            $product->update(['stock_qty' => $after]);

            return $product->stockMovements()->create([
                'user_id' => $adminId,
                'type' => $type,
                'source' => $source,
                'quantity' => $quantity,
                'before_qty' => $before,
                'after_qty' => $after,
                'note' => $note,
            ]);
        });
    }
}
