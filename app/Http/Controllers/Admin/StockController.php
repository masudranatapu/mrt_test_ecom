<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStockAdjustmentRequest;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    public function __construct(private StockService $stock) {}

    /**
     * Stock overview with quick adjust.
     */
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->when($request->search, function ($query, $search) {
                $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%"));
            })
            ->when($request->stock === 'out', fn ($q) => $q->where('stock_qty', 0))
            ->when($request->stock === 'low', fn ($q) => $q->where('stock_qty', '>', 0)->whereColumn('stock_qty', '<=', 'low_stock_alert'))
            ->orderBy('stock_qty')
            ->paginate(20)
            ->withQueryString();

        $summary = [
            'total' => Product::count(),
            'out' => Product::where('stock_qty', 0)->count(),
            'low' => Product::where('stock_qty', '>', 0)->whereColumn('stock_qty', '<=', 'low_stock_alert')->count(),
            'units' => (int) Product::sum('stock_qty'),
        ];

        return view('admin.inventory.index', compact('products', 'summary'));
    }

    /**
     * Full log of every stock change.
     */
    public function history(Request $request): View
    {
        $movements = StockMovement::query()
            ->with(['product', 'admin'])
            ->when($request->type, fn ($q, $type) => $q->where('type', $type))
            ->when($request->source, fn ($q, $source) => $q->where('source', $source))
            ->when($request->search, fn ($q, $search) => $q->whereHas('product', fn ($p) => $p
                ->where(fn ($w) => $w->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"))))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.inventory.history', compact('movements'));
    }

    public function store(StoreStockAdjustmentRequest $request, Product $product): RedirectResponse
    {
        $movement = $this->stock->adjust(
            $product,
            $request->type,
            $request->integer('quantity'),
            'manual',
            $request->note,
            $request->user('admin')->id,
        );

        $verb = $movement->type === StockMovement::TYPE_IN ? 'added to' : 'removed from';

        return back()->with('success', "{$movement->quantity} {$product->unit} {$verb} {$product->name}. New stock: {$movement->after_qty}.");
    }
}
