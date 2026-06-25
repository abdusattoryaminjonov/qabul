@extends('admin.layout')

@section('title', __('app.notifications.send_message'))

@section('content')
<div class="p-6 lg:p-10 max-w-2xl mx-auto">
    <a href="{{ route('admin.notifications.index') }}" class="inline-flex items-center gap-1.5 text-sm text-fc-muted hover:text-violet-600 font-medium mb-4">← {{ __('app.notifications.title') }}</a>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-fc">{{ __('app.notifications.send_to_admins') }}</h1>
        <p class="text-fc-muted text-sm mt-1">{{ __('app.notifications.broadcast_hint') }}</p>
    </div>

    <form method="POST" action="{{ route('admin.notifications.broadcast.store') }}" class="card p-6 space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.notifications.message_title') }}</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="input" placeholder="{{ __('app.notifications.message_title_placeholder') }}">
            @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.notifications.message_body') }}</label>
            <textarea name="message" rows="6" required class="input resize-y" placeholder="{{ __('app.notifications.message_body_placeholder') }}">{{ old('message') }}</textarea>
            @error('message')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex justify-end">
            <button type="submit" class="btn btn-primary px-8">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                {{ __('app.notifications.send') }}
            </button>
        </div>
    </form>
</div>
@endsection
