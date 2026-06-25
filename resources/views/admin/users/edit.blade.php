@extends('admin.layout')

@section('title', __('app.users.edit'))

@section('content')
<div class="p-6 lg:p-10 max-w-4xl mx-auto">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-sm text-fc-muted hover:text-violet-600 font-medium mb-4">← {{ __('app.users.title') }}</a>

    <div class="flex items-center gap-4 mb-8">
        <div class="user-avatar user-avatar-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
        <div>
            <h1 class="text-2xl font-bold text-fc">{{ $user->name }}</h1>
            <p class="text-sm text-fc-muted mt-0.5">{{ $user->email }}</p>
            <span class="permission-pill mt-2">{{ __('app.permissions.count', ['count' => count($user->permissions ?? [])]) }}</span>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="card p-6">
            <div class="section-title">
                <span class="section-title-icon">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </span>
                {{ __('app.users.account_info') }}
            </div>
            <div class="grid sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.auth.name') }}</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input">
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.common.email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input">
                    @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.users.new_password_hint') }}</label>
                    <input type="password" name="password" class="input" placeholder="{{ __('app.users.password_optional') }}">
                    @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.auth.password_confirm') }}</label>
                    <input type="password" name="password_confirmation" class="input">
                </div>
            </div>
        </div>

        @include('admin.users._permissions', ['selectedPermissions' => old('permissions', $user->permissions ?? [])])

        <div class="flex justify-end">
            <button type="submit" class="btn btn-primary px-8">{{ __('app.common.save') }}</button>
        </div>
    </form>
</div>
@endsection
