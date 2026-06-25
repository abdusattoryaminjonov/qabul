@php
    $summary = \App\Http\Controllers\Admin\NotificationController::summary($notification);
    $data = $notification->data;
    $isBroadcast = ($data['type'] ?? '') === 'broadcast';
@endphp
<div class="notification-item {{ $notification->read_at ? '' : 'notification-item-unread' }}">
    <div class="notification-item-icon notification-item-icon-{{ $summary['icon'] }}">
        @if($summary['icon'] === 'form')
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        @else
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        @endif
    </div>
    <div class="min-w-0 flex-1">
        @if($isBroadcast && ($detailed ?? false))
        <p class="text-xs font-semibold text-violet-600 uppercase tracking-wide mb-0.5">{{ __('app.notifications.broadcast') }}</p>
        <p class="text-sm font-semibold text-fc">{{ $data['title'] ?? '' }}</p>
        <p class="text-xs text-fc-muted mt-0.5">{{ __('app.notifications.from', ['name' => $data['sender_name'] ?? '']) }}</p>
        <p class="text-sm text-fc-muted mt-2 whitespace-pre-wrap leading-relaxed">{{ $data['message'] ?? '' }}</p>
        @else
        <p class="text-sm text-fc leading-snug">{{ $summary['text'] }}</p>
        @if($isBroadcast && !($detailed ?? false))
        <p class="text-xs text-fc-muted mt-0.5 line-clamp-2">{{ Str::limit($data['message'] ?? '', 80) }}</p>
        @endif
        @endif
        <p class="text-xs text-fc-muted mt-1">{{ $notification->created_at->format('d.m.Y H:i') }}</p>
    </div>
    @if(!$notification->read_at)
    <span class="notification-dot shrink-0"></span>
    @endif
</div>
