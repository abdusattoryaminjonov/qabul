@props(['variant' => 'sidebar', 'showTagline' => true])

@php
    $variants = [
        'sidebar' => ['img' => 'h-10 w-10', 'name' => 'text-white text-lg', 'tag' => 'text-sky-300/80 text-xs', 'logo' => 'dark'],
        'admin-panel' => ['img' => 'h-11 w-11', 'name' => 'text-white text-base font-bold leading-tight', 'tag' => 'text-[0.6875rem] font-medium text-sky-200/85', 'logo' => 'dark'],
        'header' => ['img' => 'h-9 w-9', 'name' => 'text-white text-base sm:text-lg', 'tag' => 'text-sky-200/80 text-[0.6875rem] sm:text-xs', 'logo' => 'dark'],
        'auth' => ['img' => 'h-14 w-14', 'name' => 'text-white text-4xl', 'tag' => 'text-sky-200 text-base', 'logo' => 'dark'],
        'compact' => ['img' => 'h-8 w-8', 'name' => 'text-fc text-base', 'tag' => 'text-fc-muted text-xs', 'logo' => 'theme'],
        'public' => ['img' => 'h-11 w-11', 'name' => 'text-[#1e3a5f] text-base font-bold leading-tight', 'tag' => 'text-[0.6875rem] font-medium text-[#64748b]', 'logo' => 'light'],
        'public-dark' => ['img' => 'h-11 w-11', 'name' => 'text-white text-base font-bold leading-tight', 'tag' => 'text-[0.6875rem] font-medium text-emerald-100/90', 'logo' => 'dark'],
    ];
    $v = $variants[$variant] ?? $variants['compact'];
    $logoDark = asset('images/npuu-logo.png');
    $logoLight = asset('images/npuu-logo-light.png');
@endphp

<div {{ $attributes->merge(['class' => 'brand-logo flex items-center gap-3 min-w-0']) }}>
    @if($v['logo'] === 'light')
    <img src="{{ $logoLight }}" alt="{{ __('app.name') }}" class="brand-logo-img {{ $v['img'] }} shrink-0 object-contain">
    @elseif($v['logo'] === 'dark')
    <img src="{{ $logoDark }}" alt="{{ __('app.name') }}" class="brand-logo-img {{ $v['img'] }} shrink-0 object-contain">
    @else
    <img src="{{ $logoLight }}" alt="{{ __('app.name') }}" class="brand-logo-img brand-logo-on-light {{ $v['img'] }} shrink-0 object-contain dark:hidden">
    <img src="{{ $logoDark }}" alt="{{ __('app.name') }}" class="brand-logo-img brand-logo-on-dark {{ $v['img'] }} shrink-0 object-contain hidden dark:block">
    @endif
    <div class="min-w-0">
        @if($variant === 'admin-panel' || $variant === 'public' || $variant === 'public-dark')
        <div class="{{ $v['name'] }} leading-tight">{{ __('app.nav.admin_panel_label') }}</div>
        <div class="{{ $v['tag'] }} truncate mt-0.5 leading-snug">{{ __('app.nav.admin_panel_tagline') }}</div>
        @else
        <div class="{{ $v['name'] }} font-bold leading-tight truncate">{{ __('app.name') }}</div>
        @if($showTagline)
        <div class="{{ $v['tag'] }} truncate mt-0.5 leading-snug">{{ __('app.tagline') }}</div>
        @endif
        @endif
    </div>
</div>
