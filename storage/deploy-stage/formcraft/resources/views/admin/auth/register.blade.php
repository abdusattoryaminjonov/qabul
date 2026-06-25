@extends('admin.layout')

@section('title', __('app.auth.register'))

@section('content')
<div class="min-h-screen flex">
    <div class="hidden lg:flex lg:w-1/2 auth-panel items-center justify-center p-12">
        <div class="relative z-10 max-w-md">
            <h1 class="text-4xl font-bold text-white mb-4">{{ __('app.auth.register_title') }}</h1>
            <p class="text-violet-200 text-lg leading-relaxed">{{ __('app.auth.register_desc') }}</p>
            <ul class="mt-8 space-y-3">
                @foreach(__('app.auth.features') as $feature)
                <li class="flex items-center gap-3 text-violet-100">
                    <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ $feature }}
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="flex-1 flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md">
            <div class="card p-8">
                <h2 class="text-xl font-bold text-fc mb-1">{{ __('app.auth.create_account') }}</h2>
                <p class="text-fc-muted text-sm mb-6">{{ __('app.auth.register_subtitle') }}</p>
                <form method="POST" action="{{ route('admin.register') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.auth.name') }}</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="input">
                        @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
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
                    <button type="submit" class="btn btn-primary w-full py-3">{{ __('app.auth.register') }}</button>
                </form>
                <p class="text-center text-sm text-fc-muted mt-6">
                    {{ __('app.auth.have_account') }} <a href="{{ route('admin.login') }}" class="text-violet-600 font-semibold">{{ __('app.auth.login') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
