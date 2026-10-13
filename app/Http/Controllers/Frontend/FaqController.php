<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::active()->orderBy('sort_order')->get()->groupBy('category');

        // Keep the category order defined on the model
        $groups = collect(Faq::CATEGORIES)
            ->filter(fn ($meta, $key) => $faqs->has($key))
            ->map(fn ($meta, $key) => $meta + ['items' => $faqs[$key]]);

        return view('frontend.faq', compact('groups'));
    }
}
