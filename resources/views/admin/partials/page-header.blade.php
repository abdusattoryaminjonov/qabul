@props([
    'title',
    'count' => null,
    'icon' => null,
    'createUrl' => null,
    'createLabel' => null,
])

<div class="admin-panel-head">
    <div class="admin-panel-head-left">
        @if($icon)
        <div class="admin-panel-icon" aria-hidden="true">{!! $icon !!}</div>
        @endif
        <div>
            <h1 class="admin-panel-title">{{ $title }}</h1>
            @if($count !== null)
            <p class="admin-panel-count">{{ __('app.common.records_count', ['count' => $count]) }}</p>
            @endif
        </div>
    </div>
    @if($createUrl && $createLabel)
    <div class="admin-panel-head-right">
        <a href="{{ $createUrl }}" class="btn btn-primary btn-create">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            {{ $createLabel }}
        </a>
    </div>
    @endif
</div>
