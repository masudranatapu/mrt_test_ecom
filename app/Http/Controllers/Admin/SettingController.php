<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateContactSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function editContact(): View
    {
        return view('admin.settings.contact', ['settings' => Setting::allCached()]);
    }

    public function updateContact(UpdateContactSettingsRequest $request): RedirectResponse
    {
        Setting::setMany($request->validated());

        return back()->with('success', 'Contact & store information saved.');
    }
}
