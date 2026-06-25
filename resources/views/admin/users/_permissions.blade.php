<div class="card p-6">
    <div class="section-title">
        <span class="section-title-icon">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </span>
        {{ __('app.permissions.title') }}
    </div>
    <p class="text-xs text-fc-muted mb-5 -mt-2">{{ __('app.permissions.subtitle') }}</p>

    <p class="text-xs font-semibold text-fc-muted uppercase tracking-wide mb-3">{{ __('app.permissions.group_forms') }}</p>
    <div class="permission-list mb-6">
        @foreach(['view_all_forms', 'edit_any_form', 'delete_any_form'] as $key)
        @if(isset($permissionLabels[$key]))
        <label class="permission-check">
            <input type="checkbox" name="permissions[]" value="{{ $key }}" class="permission-check-input"
                @checked(in_array($key, old('permissions', $selectedPermissions ?? []), true))>
            <div class="permission-check-body">
                <span class="permission-check-label">{{ $permissionLabels[$key] }}</span>
                <span class="permission-check-desc">{{ $permissionDescriptions[$key] }}</span>
            </div>
        </label>
        @endif
        @endforeach
    </div>

    <p class="text-xs font-semibold text-fc-muted uppercase tracking-wide mb-3">{{ __('app.permissions.group_responses') }}</p>
    <div class="permission-list">
        @foreach(['view_any_responses', 'export_any_responses', 'delete_any_responses'] as $key)
        @if(isset($permissionLabels[$key]))
        <label class="permission-check">
            <input type="checkbox" name="permissions[]" value="{{ $key }}" class="permission-check-input"
                @checked(in_array($key, old('permissions', $selectedPermissions ?? []), true))>
            <div class="permission-check-body">
                <span class="permission-check-label">{{ $permissionLabels[$key] }}</span>
                <span class="permission-check-desc">{{ $permissionDescriptions[$key] }}</span>
            </div>
        </label>
        @endif
        @endforeach
    </div>

    @error('permissions')<p class="text-red-500 text-sm mt-3">{{ $message }}</p>@enderror
    @error('permissions.*')<p class="text-red-500 text-sm mt-3">{{ $message }}</p>@enderror
</div>
