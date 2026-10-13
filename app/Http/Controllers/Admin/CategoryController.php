<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private FileUploadService $uploader) {}

    public function index(Request $request): View
    {
        $categories = Category::query()
            ->with('parent')
            ->withCount(['products', 'children'])
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderByRaw('COALESCE(parent_id, id), parent_id IS NOT NULL, sort_order')
            ->paginate(20)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            'category' => new Category(['status' => true, 'sort_order' => 0]),
            'parents' => $this->parentOptions(),
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['sort_order'] ??= 0;

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploader->upload($request->file('image'), 'categories');
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
            'parents' => $this->parentOptions($category),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['sort_order'] ??= 0;

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploader->upload($request->file('image'), 'categories', $category->image);
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->withTrashed()->exists()) {
            return back()->with('error', 'This category has products. Move or delete them first.');
        }

        if ($category->children()->exists()) {
            return back()->with('error', 'This category has sub-categories. Delete them first.');
        }

        $this->uploader->delete($category->image);
        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }

    /**
     * Only top-level categories can be parents (two levels deep).
     */
    private function parentOptions(?Category $except = null)
    {
        return Category::root()
            ->when($except, fn ($q) => $q->whereKeyNot($except->id))
            ->orderBy('sort_order')
            ->pluck('name', 'id');
    }
}
