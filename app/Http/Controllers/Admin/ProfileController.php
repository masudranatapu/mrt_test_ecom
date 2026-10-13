<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private FileUploadService $uploader) {}

    public function edit(Request $request): View
    {
        return view('admin.profile.edit', ['admin' => $request->user('admin')]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $admin = $request->user('admin');
        $data = $request->safe()->except('avatar');

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->uploader->upload($request->file('avatar'), 'admins', $admin->avatar);
        }

        $admin->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user('admin')->update(['password' => $request->password]);

        return back()->with('success', 'Password changed successfully.');
    }
}
