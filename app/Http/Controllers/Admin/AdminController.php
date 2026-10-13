<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(private FileUploadService $uploader) {}

    public function index(Request $request): View
    {
        $admins = User::query()
            ->when($request->search, function ($query, $search) {
                $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.admins.index', compact('admins'));
    }

    public function create(): View
    {
        return view('admin.admins.create', ['admin' => new User(['status' => true])]);
    }

    public function store(StoreAdminRequest $request): RedirectResponse
    {
        $data = $this->withPermissions($request->safe()->except('avatar'));

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->uploader->upload($request->file('avatar'), 'admins');
        }

        User::create($data);

        return redirect()->route('admin.admins.index')->with('success', 'Admin created successfully.');
    }

    public function edit(User $admin): View
    {
        return view('admin.admins.edit', compact('admin'));
    }

    public function update(UpdateAdminRequest $request, User $admin): RedirectResponse
    {
        $data = $request->safe()->except(['avatar', 'password']);

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->uploader->upload($request->file('avatar'), 'admins', $admin->avatar);
        }

        // An admin cannot deactivate or demote their own account
        if ($admin->is($request->user('admin'))) {
            $data['status'] = true;
            $data['is_super_admin'] = true;
        }

        $admin->update($this->withPermissions($data));

        return redirect()->route('admin.admins.index')->with('success', 'Admin updated successfully.');
    }

    /**
     * Super admins have every permission, so their list is stored empty.
     */
    private function withPermissions(array $data): array
    {
        $data['permissions'] = ! empty($data['is_super_admin']) ? [] : array_values($data['permissions'] ?? []);

        return $data;
    }

    public function destroy(Request $request, User $admin): RedirectResponse
    {
        if ($admin->is($request->user('admin'))) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $this->uploader->delete($admin->avatar);
        $admin->delete();

        return back()->with('success', 'Admin deleted successfully.');
    }
}
