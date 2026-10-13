@php($isSelf = $admin->exists && $admin->is(auth('admin')->user()))

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Admin Information</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label required">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $admin->name) }}"
                        class="form-control @error('name') is-invalid @enderror">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label required">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $admin->email) }}"
                            class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $admin->phone) }}"
                            class="form-control @error('phone') is-invalid @enderror">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" @class(['form-label', 'required' => ! $admin->exists])>Password</label>
                        <input type="password" id="password" name="password"
                            class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if ($admin->exists)
                            <div class="form-text">Leave blank to keep the current password.</div>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" @class(['form-label', 'required' => ! $admin->exists])>Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        {{-- Role & permissions --}}
        @php($isSuper = (bool) old('is_super_admin', $admin->exists ? $admin->is_super_admin : false))
        @php($granted = old('permissions', $admin->permissions ?? []))
        <div class="card mt-4">
            <div class="card-header">Role &amp; Permissions</div>
            <div class="card-body">
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="role-option">
                            <input type="radio" name="is_super_admin" value="0" @checked(! $isSuper) @disabled($isSelf)>
                            <span>
                                <span class="fw-semibold d-block"><i class="bi bi-person-badge me-1"></i> Staff</span>
                                <span class="small text-muted">Can only use the sections ticked below.</span>
                            </span>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="role-option">
                            <input type="radio" name="is_super_admin" value="1" @checked($isSuper) @disabled($isSelf)>
                            <span>
                                <span class="fw-semibold d-block"><i class="bi bi-shield-check me-1"></i> Super Admin</span>
                                <span class="small text-muted">Full access, can manage admins and staff.</span>
                            </span>
                        </label>
                    </div>
                </div>
                @if ($isSelf)
                    <input type="hidden" name="is_super_admin" value="1">
                    <div class="form-text mb-2">You cannot change your own role.</div>
                @endif
                @error('is_super_admin')<div class="text-danger small mb-2">{{ $message }}</div>@enderror

                <div id="permissionList" @class(['opacity-50' => $isSuper])>
                    <div class="small fw-semibold text-muted text-uppercase mb-2">Permissions for staff</div>
                    <div class="row g-2">
                        @foreach (\App\Models\User::PERMISSIONS as $key => $permission)
                            <div class="col-md-6">
                                <label class="perm-option">
                                    <input class="form-check-input mt-1" type="checkbox" name="permissions[]" value="{{ $key }}"
                                        @checked(in_array($key, $granted, true)) @disabled($isSuper)>
                                    <span>
                                        <span class="fw-semibold d-block small">{{ $permission['label'] }}</span>
                                        <span class="text-muted" style="font-size:.75rem">{{ $permission['description'] }}</span>
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('permissions.*')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">Photo</div>
            <div class="card-body text-center">
                <img src="{{ $admin->exists ? $admin->avatar_url : default_avatar() }}" {!! img_fallback(true) !!}
                    id="avatarPreview" class="rounded-circle avatar-lg mb-3" alt="">
                <input type="file" name="avatar" accept="image/*" data-preview="#avatarPreview"
                    class="form-control form-control-sm @error('avatar') is-invalid @enderror">
                @error('avatar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="card">
            <div class="card-header">Status</div>
            <div class="card-body">
                <input type="hidden" name="status" value="0">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1"
                        @checked(old('status', $admin->status)) @disabled($isSelf)>
                    <label class="form-check-label" for="status">Active</label>
                </div>
                @if ($isSelf)
                    <input type="hidden" name="status" value="1">
                    <div class="form-text">You cannot deactivate your own account.</div>
                @else
                    <div class="form-text">Inactive admins cannot log in.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> {{ $admin->exists ? 'Update' : 'Create' }} Admin</button>
    <a href="{{ route('admin.admins.index') }}" class="btn btn-light">Cancel</a>
</div>

@push('scripts')
    <script>
        // Super admins have every permission: disable the checkboxes while that role is chosen
        document.querySelectorAll('input[name="is_super_admin"]').forEach((radio) => radio.addEventListener('change', () => {
            const isSuper = document.querySelector('input[name="is_super_admin"]:checked')?.value === '1';
            const list = document.getElementById('permissionList');
            list.classList.toggle('opacity-50', isSuper);
            list.querySelectorAll('input').forEach((i) => (i.disabled = isSuper));
        }));
    </script>
@endpush
