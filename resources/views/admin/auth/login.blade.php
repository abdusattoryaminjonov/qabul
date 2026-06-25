@extends('admin.layout')

@section('title', __('app.auth.login'))

@section('content')
<div class="min-h-screen flex">
    <div class="hidden lg:flex lg:w-1/2 auth-panel items-center justify-center p-12">
        <div class="relative z-10 max-w-md">
            <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur flex items-center justify-center mb-8 border border-white/20">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
            </div>
            <h1 class="text-4xl font-bold text-white mb-4 leading-tight">{{ __('app.name') }}</h1>
            <p class="text-violet-200 text-lg leading-relaxed">{{ __('app.auth.brand_title') }}. {{ __('app.auth.brand_desc') }}</p>
            <div class="mt-10 grid grid-cols-2 gap-4">
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 border border-white/10">
                    <div class="text-2xl font-bold text-white">9+</div>
                    <div class="text-violet-300 text-sm mt-1">{{ __('app.auth.stats_types') }}</div>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 border border-white/10">
                    <div class="text-2xl font-bold text-white">∞</div>
                    <div class="text-violet-300 text-sm mt-1">{{ __('app.auth.stats_forms') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md">
            <div class="lg:hidden text-center mb-8">
                <h1 class="text-2xl font-bold text-fc">{{ __('app.name') }}</h1>
            </div>
            <div class="card p-8">
                <h2 class="text-xl font-bold text-fc mb-1">{{ __('app.auth.welcome') }}</h2>
                <p class="text-fc-muted text-sm mb-6">{{ __('app.auth.login_subtitle') }}</p>
                <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.common.email') }}</label>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus class="input">
                        @error('email')<p class="text-red-500 text-sm mt-1.5">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.auth.password') }}</label>
                        <input type="password" name="password" required class="input">
                    </div>
                    <label class="flex items-center gap-2.5 text-sm text-fc-muted cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-violet-600">
                        {{ __('app.auth.remember') }}
                    </label>
                    <button type="submit" class="btn btn-primary w-full py-3">{{ __('app.auth.login') }}</button>
                </form>
                <p class="text-center text-sm text-fc-muted mt-6">
                    {{ __('app.auth.contact_admin') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
