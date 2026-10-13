<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\UpdatePasswordRequest;
use App\Http\Requests\Customer\UpdateProfileRequest;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private FileUploadService $uploader) {}

    public function edit(Request $request): View
    {
        return view('customer.profile.edit', ['customer' => $request->user('customer')]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $customer = $request->user('customer');
        $data = $request->safe()->except('avatar');

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->uploader->upload($request->file('avatar'), 'customers', $customer->avatar);
        }

        $customer->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user('customer')->update(['password' => $request->password]);

        return back()->with('success', 'Password changed successfully.');
    }
}
