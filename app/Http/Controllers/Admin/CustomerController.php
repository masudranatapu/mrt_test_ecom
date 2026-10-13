<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateCustomerApprovalRequest;
use App\Http\Requests\Admin\UpdateCustomerStatusRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->when($request->search, function ($query, $search) {
                $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%"));
            })
            ->when($request->approval === 'approved', fn ($q) => $q->where('is_approved', true))
            ->when($request->approval === 'pending', fn ($q) => $q->where('is_approved', false))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->boolean('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer): View
    {
        $customer->load('addresses');

        return view('admin.customers.show', [
            'customer' => $customer,
            'orders' => $customer->orders()->withCount('items')->limit(10)->get(),
            'orderStats' => [
                'count' => $customer->orders()->count(),
                'spent' => (float) $customer->orders()->where('status', '!=', 'cancelled')->sum('total'),
            ],
        ]);
    }

    public function updateApproval(UpdateCustomerApprovalRequest $request, Customer $customer): RedirectResponse
    {
        $approved = $request->boolean('is_approved');

        $customer->update([
            'is_approved' => $approved,
            'approved_at' => $approved ? now() : null,
        ]);

        return back()->with('success', $approved ? 'Customer approved.' : 'Customer approval removed.');
    }

    public function updateStatus(UpdateCustomerStatusRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update(['status' => $request->boolean('status')]);

        return back()->with('success', $customer->status ? 'Customer activated.' : 'Customer deactivated.');
    }
}
