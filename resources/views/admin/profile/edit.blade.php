@extends('admin.layout')

@section('title', __('app.profile.title'))

@section('content')
<div class="p-6 lg:p-10 max-w-2xl mx-auto">
    <div class="profile-hero p-6 mb-6 flex items-center gap-5">
        <div class="user-avatar user-avatar-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
        <div class="min-w-0">
            <h1 class="text-xl font-bold text-fc truncate">{{ $user->name }}</h1>
            <p class="text-sm text-fc-muted truncate">{{ $user->email }}</p>
            <p class="text-sm font-medium text-fc mt-1">{{ $user->roleLabel() }}</p>
        </div>
    </div>

    <div class="card p-6 mb-6">
        <div class="section-title">
            <span class="section-title-icon">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </span>
            {{ __('app.profile.info') }}
        </div>
        <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.auth.name') }}</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input">
                @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.common.email') }}</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input">
                @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn btn-primary">{{ __('app.common.save') }}</button>
        </form>
    </div>

    <div class="card p-6">
        <div class="section-title">
            <span class="section-title-icon">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </span>
            {{ __('app.profile.change_password') }}
        </div>
        <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.profile.current_password') }}</label>
                <input type="password" name="current_password" required class="input" autocomplete="current-password">
                @error('current_password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.profile.new_password') }}</label>
                <input type="password" name="password" required class="input" autocomplete="new-password">
                @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.auth.password_confirm') }}</label>
                <input type="password" name="password_confirmation" required class="input" autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-secondary">{{ __('app.profile.update_password') }}</button>
        </form>
    </div>
</div>
@endsection
