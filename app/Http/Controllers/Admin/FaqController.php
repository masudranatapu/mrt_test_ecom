<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFaqRequest;
use App\Http\Requests\Admin\UpdateFaqRequest;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(Request $request): View
    {
        $faqs = Faq::query()
            ->when($request->category, fn ($q, $category) => $q->where('category', $category))
            ->when($request->search, fn ($q, $search) => $q->where('question', 'like', "%{$search}%"))
            ->orderBy('category')
            ->orderBy('sort_order')
            ->paginate(20)
            ->withQueryString();

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create(): View
    {
        return view('admin.faqs.create', ['faq' => new Faq(['status' => true, 'sort_order' => 0, 'category' => 'general'])]);
    }

    public function store(StoreFaqRequest $request): RedirectResponse
    {
        Faq::create(['sort_order' => $request->integer('sort_order')] + $request->validated());

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ added successfully.');
    }

    public function edit(Faq $faq): View
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(UpdateFaqRequest $request, Faq $faq): RedirectResponse
    {
        $faq->update(['sort_order' => $request->integer('sort_order')] + $request->validated());

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated successfully.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return back()->with('success', 'FAQ deleted successfully.');
    }
}
