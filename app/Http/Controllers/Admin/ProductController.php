<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\StockMovement;
use App\Services\FileUploadService;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private FileUploadService $uploader,
        private StockService $stock,
    ) {}

    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->when($request->search, function ($query, $search) {
                $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%"));
            })
            ->when($request->category, fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->boolean('status')))
            ->when($request->stock === 'out', fn ($q) => $q->where('stock_qty', 0))
            ->when($request->stock === 'low', fn ($q) => $q->where('stock_qty', '>', 0)->whereColumn('stock_qty', '<=', 'low_stock_alert'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'product' => new Product(['status' => true, 'unit' => 'pcs', 'stock_qty' => 0, 'low_stock_alert' => 10]),
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = DB::transaction(function () use ($request) {
            $data = $request->safe()->except(['thumbnail', 'images', 'stock_qty']);
            $data['sku'] ??= 'TMP-'.uniqid();

            if ($request->hasFile('thumbnail')) {
                $data['thumbnail'] = $this->uploader->upload($request->file('thumbnail'), 'products');
            }

            $product = Product::create($data + ['stock_qty' => 0]);

            if (str_starts_with($product->sku, 'TMP-')) {
                $product->update(['sku' => 'PRD-'.str_pad($product->id, 5, '0', STR_PAD_LEFT)]);
            }

            $this->storeGallery($request, $product);

            // Opening stock is logged like any other stock change
            if ($request->integer('stock_qty') > 0) {
                $this->stock->adjust($product, StockMovement::TYPE_IN, $request->integer('stock_qty'), 'initial', 'Opening stock', $request->user('admin')->id);
            }

            return $product;
        });

        return redirect()->route('admin.products.show', $product)->with('success', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'images']);

        return view('admin.products.show', [
            'product' => $product,
            'movements' => $product->stockMovements()->with('admin')->paginate(10),
        ]);
    }

    public function edit(Product $product): View
    {
        $product->load('images');

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->safe()->except(['thumbnail', 'images']);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->uploader->upload($request->file('thumbnail'), 'products', $product->thumbnail);
        }

        $product->update($data);
        $this->storeGallery($request, $product);

        return redirect()->route('admin.products.show', $product)->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Soft delete: old orders still point at this product
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    public function destroyImage(ProductImage $image): RedirectResponse
    {
        $this->uploader->delete($image->image);
        $image->delete();

        return back()->with('success', 'Image removed.');
    }

    private function storeGallery(Request $request, Product $product): void
    {
        $order = (int) $product->images()->max('sort_order');

        foreach ($request->file('images', []) as $file) {
            $product->images()->create([
                'image' => $this->uploader->upload($file, 'products/gallery'),
                'sort_order' => ++$order,
            ]);
        }
    }

    /**
     * Categories grouped as "Parent" / "Parent › Child" for select boxes.
     */
    private function categoryOptions()
    {
        return Category::with('children')
            ->root()
            ->orderBy('sort_order')
            ->get()
            ->flatMap(fn (Category $parent) => collect([$parent->id => $parent->name])
                ->union($parent->children->mapWithKeys(fn ($child) => [$child->id => $parent->name.' › '.$child->name])));
    }
}
