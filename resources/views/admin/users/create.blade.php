@extends('admin.layout')

@section('title', __('app.users.create'))

@section('content')
<div class="user-create-page p-4 sm:p-6 lg:p-8 max-w-6xl mx-auto">
    <a href="{{ route('admin.users.index') }}" class="user-create-back">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        {{ __('app.users.title') }}
    </a>

    <header class="user-create-hero">
        <p class="user-create-eyebrow">{{ __('app.nav.admin_panel') }}</p>
        <h1 class="user-create-title">{{ __('app.users.create') }}</h1>
        <p class="user-create-subtitle">{{ __('app.permissions.subtitle') }}</p>
    </header>

    <div class="user-create-shell">
        @if ($errors->any())
            <div class="user-create-errors">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="user-create-layout">
                <section class="user-create-panel">
                    <div class="user-create-panel-head">
                        <span class="user-create-step">1</span>
                        <div>
                            <h2 class="user-create-panel-title">{{ __('app.users.account_info') }}</h2>
                        </div>
                    </div>

                    <div class="user-create-fields">
                        <div class="user-create-field-row">
                            <div>
                                <label class="user-create-label">{{ __('app.auth.name') }} *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required class="input">
                                @error('name')<p class="user-create-field-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="user-create-label">{{ __('app.common.email') }} *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required class="input">
                                @error('email')<p class="user-create-field-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="user-create-field-row">
                            <div>
                                <label class="user-create-label">{{ __('app.auth.password') }} *</label>
                                <input type="password" name="password" required class="input">
                                @error('password')<p class="user-create-field-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="user-create-label">{{ __('app.auth.password_confirm') }} *</label>
                                <input type="password" name="password_confirmation" required class="input">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="user-create-panel">
                    <div class="user-create-panel-head">
                        <span class="user-create-step">2</span>
                        <div>
                            <h2 class="user-create-panel-title">{{ __('app.permissions.title') }}</h2>
                            <p class="user-create-panel-desc">{{ __('app.permissions.subtitle') }}</p>
                        </div>
                    </div>

                    @include('admin.users._permissions', [
                        'selectedPermissions' => old('permissions', []),
                        'compactLayout' => true,
                    ])
                </section>
            </div>

            <div class="user-create-actions">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary hidden sm:inline-flex">{{ __('app.common.cancel') }}</a>
                <button type="submit" class="btn btn-primary user-create-submit">{{ __('app.users.create') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
