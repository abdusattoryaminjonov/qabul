@extends('admin.layout')

@section('title', __('app.notifications.title'))

@section('content')
<div class="p-6 lg:p-10">
    <div class="flex flex-wrap justify-between items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-fc">{{ __('app.notifications.title') }}</h1>
            <p class="text-fc-muted text-sm mt-1">{{ __('app.notifications.subtitle') }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if(auth()->user()->isSuperAdmin())
            <a href="{{ route('admin.notifications.broadcast.create') }}" class="btn btn-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                {{ __('app.notifications.send_message') }}
            </a>
            @endif
            @if(auth()->user()->unreadNotifications()->exists())
            <form action="{{ route('admin.notifications.read-all') }}" method="POST">@csrf
                <button type="submit" class="btn btn-secondary">{{ __('app.notifications.mark_all_read') }}</button>
            </form>
            @endif
        </div>
    </div>

    @if($notifications->isEmpty())
    <div class="card p-14 text-center">
        <div class="w-14 h-14 rounded-2xl bg-violet-500/10 text-violet-600 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        </div>
        <p class="text-fc-muted">{{ __('app.notifications.empty') }}</p>
    </div>
    @else
    <div class="card overflow-hidden divide-y divide-[var(--fc-border)]">
        @foreach($notifications as $notification)
        @include('admin.notifications._item', ['notification' => $notification, 'detailed' => true])
        @endforeach
    </div>
    <div class="mt-6">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
