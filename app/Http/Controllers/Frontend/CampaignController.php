<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function show(Campaign $campaign): View
    {
        abort_unless($campaign->status, 404);

        $products = $campaign->products()->active()->with('category')->orderBy('name')->paginate(16);

        return view('frontend.campaign', compact('campaign', 'products'));
    }
}
