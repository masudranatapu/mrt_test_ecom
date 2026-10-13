<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreAddressRequest;
use App\Http\Requests\Customer\UpdateAddressRequest;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(Request $request): View
    {
        return view('customer.addresses.index', [
            'addresses' => $request->user('customer')->addresses()->get(),
        ]);
    }

    public function create(): View
    {
        return view('customer.addresses.create', ['address' => new CustomerAddress(['label' => 'Home'])]);
    }

    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $customer = $request->user('customer');

        DB::transaction(function () use ($request, $customer) {
            // The first address is always the default one
            $makeDefault = $request->boolean('is_default') || ! $customer->addresses()->exists();

            if ($makeDefault) {
                $this->clearDefault($customer);
            }

            $customer->addresses()->create(['is_default' => $makeDefault] + $request->safe()->except('is_default'));
        });

        return redirect()->route('customer.addresses.index')->with('success', 'Address added successfully.');
    }

    public function edit(Request $request, CustomerAddress $address): View
    {
        $this->ensureOwner($request, $address);

        return view('customer.addresses.edit', compact('address'));
    }

    public function update(UpdateAddressRequest $request, CustomerAddress $address): RedirectResponse
    {
        DB::transaction(function () use ($request, $address) {
            $data = $request->safe()->except('is_default');

            if ($request->boolean('is_default')) {
                $this->clearDefault($request->user('customer'));
                $data['is_default'] = true;
            }

            $address->update($data);
        });

        return redirect()->route('customer.addresses.index')->with('success', 'Address updated successfully.');
    }

    public function destroy(Request $request, CustomerAddress $address): RedirectResponse
    {
        $this->ensureOwner($request, $address);

        $customer = $request->user('customer');
        $wasDefault = $address->is_default;
        $address->delete();

        // Promote another address so the customer always has a default
        if ($wasDefault) {
            $customer->addresses()->latest()->first()?->update(['is_default' => true]);
        }

        return back()->with('success', 'Address deleted successfully.');
    }

    private function ensureOwner(Request $request, CustomerAddress $address): void
    {
        abort_unless($address->customer_id === $request->user('customer')->id, 403);
    }

    private function clearDefault(Customer $customer): void
    {
        $customer->addresses()->where('is_default', true)->update(['is_default' => false]);
    }
}
