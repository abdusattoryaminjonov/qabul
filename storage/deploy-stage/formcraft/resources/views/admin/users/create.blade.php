@extends('admin.layout')

@section('title', __('app.users.create'))

@section('content')
<div class="p-6 lg:p-10 max-w-4xl mx-auto">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-sm text-fc-muted hover:text-violet-600 font-medium mb-4">← {{ __('app.users.title') }}</a>
    <h1 class="text-2xl font-bold text-fc mb-8">{{ __('app.users.create') }}</h1>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
        @csrf

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
                    <input type="text" name="name" value="{{ old('name') }}" required class="input">
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.common.email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="input">
                    @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.auth.password') }}</label>
                    <input type="password" name="password" required class="input">
                    @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.auth.password_confirm') }}</label>
                    <input type="password" name="password_confirmation" required class="input">
                </div>
            </div>
        </div>

        @include('admin.users._permissions', ['selectedPermissions' => old('permissions', [])])

        <div class="flex justify-end">
            <button type="submit" class="btn btn-primary px-8">{{ __('app.users.create') }}</button>
        </div>
    </form>
</div>
@endsection
