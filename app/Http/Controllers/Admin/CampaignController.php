<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCampaignRequest;
use App\Http\Requests\Admin\UpdateCampaignRequest;
use App\Models\Campaign;
use App\Models\Category;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function __construct(private FileUploadService $uploader) {}

    public function index(): View
    {
        $campaigns = Campaign::withCount('products')->latest('starts_at')->paginate(15);

        return view('admin.campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        return view('admin.campaigns.create', [
            'campaign' => new Campaign([
                'status' => true,
                'is_featured' => true,
                'discount_percent' => 10,
                'starts_at' => now()->startOfHour(),
                'ends_at' => now()->startOfHour()->addWeek(),
            ]),
            'categories' => $this->productsByCategory(),
            'selected' => [],
        ]);
    }

    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['banner', 'product_ids']);

        if ($request->hasFile('banner')) {
            $data['banner'] = $this->uploader->upload($request->file('banner'), 'campaigns');
        }

        $campaign = Campaign::create($data);
        $campaign->products()->sync($request->validated('product_ids'));

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign created.');
    }

    public function edit(Campaign $campaign): View
    {
        return view('admin.campaigns.edit', [
            'campaign' => $campaign,
            'categories' => $this->productsByCategory(),
            'selected' => $campaign->products()->pluck('products.id')->all(),
        ]);
    }

    public function update(UpdateCampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        $data = $request->safe()->except(['banner', 'product_ids']);

        if ($request->hasFile('banner')) {
            $data['banner'] = $this->uploader->upload($request->file('banner'), 'campaigns', $campaign->banner);
        }

        $campaign->update($data);
        $campaign->products()->sync($request->validated('product_ids'));

        return redirect()->route('admin.campaigns.index')->with('success', 'Campaign updated.');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        $this->uploader->delete($campaign->banner);
        $campaign->delete();

        return back()->with('success', 'Campaign deleted.');
    }

    /** Active products grouped by category, for the product picker. */
    private function productsByCategory()
    {
        return Category::with(['products' => fn ($q) => $q->active()->orderBy('name')->select('id', 'category_id', 'name', 'price')])
            ->whereHas('products', fn ($q) => $q->active())
            ->orderBy('name')
            ->get();
    }
}
