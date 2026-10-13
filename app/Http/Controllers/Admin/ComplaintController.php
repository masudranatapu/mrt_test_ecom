<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateComplaintRequest;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function index(Request $request): View
    {
        $complaints = Complaint::query()
            ->with(['customer', 'order'])
            ->when($request->search, function ($query, $search) {
                $query->where(fn ($q) => $q->where('complaint_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%"));
            })
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->category, fn ($q, $category) => $q->where('category', $category))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statusCounts = Complaint::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.complaints.index', compact('complaints', 'statusCounts'));
    }

    public function show(Complaint $complaint): View
    {
        $complaint->load(['customer', 'order']);

        return view('admin.complaints.show', compact('complaint'));
    }

    public function update(UpdateComplaintRequest $request, Complaint $complaint): RedirectResponse
    {
        $resolved = in_array($request->status, ['resolved', 'closed'], true);

        $complaint->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
            'resolved_at' => $resolved ? ($complaint->resolved_at ?? now()) : null,
        ]);

        return back()->with('success', 'Complaint updated.');
    }
}
