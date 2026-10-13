<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreComplaintRequest;
use App\Models\Complaint;
use App\Models\User;
use App\Notifications\AdminAlertNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    public function create(Request $request): View
    {
        $customer = auth('customer')->user();

        return view('frontend.complaint', [
            'customer' => $customer,
            'orders' => $customer ? $customer->orders()->limit(30)->get(['id', 'order_number', 'created_at', 'total']) : collect(),
            'selectedOrder' => $request->integer('order') ?: null,
        ]);
    }

    public function store(StoreComplaintRequest $request): RedirectResponse
    {
        $complaint = Complaint::create($request->safe()->except('website') + [
            'customer_id' => auth('customer')->id(),
            'status' => 'open',
        ]);

        $complaint->update(['complaint_number' => 'CMP-'.now()->format('ymd').'-'.str_pad($complaint->id, 4, '0', STR_PAD_LEFT)]);

        rescue(fn () => Notification::send(User::withPermission('support'), new AdminAlertNotification(
            "New complaint {$complaint->complaint_number} from {$complaint->name}",
            route('admin.complaints.show', $complaint),
            'bi-exclamation-octagon',
            $complaint->subject,
        )));

        return back()->with('success', "Your complaint has been filed. Reference number: {$complaint->complaint_number}. Our team will contact you soon.");
    }
}
