<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ __('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/theme.js', 'resources/js/app.js'])
    @stack('scripts')
</head>
<body class="min-h-screen @auth admin-bg @else auth-page @endauth">

    @auth
    <div class="flex min-h-screen" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
        <div x-show="sidebarOpen" x-transition.opacity
            class="sidebar-backdrop fixed inset-0 z-40 bg-black/50 lg:hidden"
            @click="sidebarOpen = false" style="display: none;" aria-hidden="true"></div>

        <aside class="sidebar sidebar-drawer flex flex-col w-64 shrink-0 fixed inset-y-0 left-0 z-50"
            :class="sidebarOpen ? 'sidebar-drawer-open' : ''">
            <div class="p-6 border-b border-white/10 flex items-center justify-between gap-3">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 min-w-0" @click="sidebarOpen = false">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-purple-700 flex items-center justify-center shadow-lg shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-white text-lg leading-tight truncate">{{ __('app.name') }}</div>
                        <div class="text-xs text-violet-300/70 truncate">{{ __('app.tagline') }}</div>
                    </div>
                </a>
                <button type="button" class="sidebar-close lg:hidden" @click="sidebarOpen = false" aria-label="{{ __('app.nav.close_menu') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                @include('admin.partials.sidebar-nav')
            </nav>
        </aside>

        <div class="flex-1 lg:ml-64 min-w-0 flex flex-col w-full">
            <header class="admin-header sticky top-0 z-30 px-3 sm:px-4 lg:px-6 flex items-center justify-between gap-2 sm:gap-3">
                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                    <button type="button" class="mobile-menu-btn lg:hidden" @click="sidebarOpen = true" aria-label="{{ __('app.nav.open_menu') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="min-w-0 lg:hidden">
                        <a href="{{ route('admin.dashboard') }}" class="font-bold text-fc truncate block">{{ __('app.name') }}</a>
                    </div>
                    <div class="hidden lg:block min-w-0">
                        @hasSection('header_title')
                        <h1 class="text-sm font-semibold text-fc truncate">@yield('header_title')</h1>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-1.5 sm:gap-2 lg:gap-3 ml-auto shrink-0">
                    <x-locale-theme />

                    <div class="hidden sm:block w-px h-6 bg-[var(--fc-border)]"></div>

                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" @click="open = !open"
                            class="header-notify {{ request()->routeIs('admin.notifications.*') ? 'ring-2 ring-violet-500/30' : '' }}"
                            aria-label="{{ __('app.notifications.title') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @if(($headerNotificationCount ?? 0) > 0)
                            <span class="header-notify-badge">{{ $headerNotificationCount > 9 ? '9+' : $headerNotificationCount }}</span>
                            @endif
                        </button>

                        <div x-show="open" x-transition
                            class="header-dropdown header-notify-dropdown absolute right-0 mt-2 w-[min(20rem,calc(100vw-1.5rem))] py-1.5 z-50"
                            style="display: none;">
                            <div class="px-4 py-2.5 border-b border-[var(--fc-border)] flex items-center justify-between gap-2">
                                <span class="text-sm font-semibold text-fc">{{ __('app.notifications.title') }}</span>
                                @if(($headerNotificationCount ?? 0) > 0)
                                <form action="{{ route('admin.notifications.read-all') }}" method="POST">@csrf
                                    <button type="submit" class="text-xs text-violet-600 font-medium hover:underline">{{ __('app.notifications.mark_all_read') }}</button>
                                </form>
                                @endif
                            </div>
                            @forelse($headerNotifications ?? [] as $notification)
                            @include('admin.notifications._item', ['notification' => $notification])
                            @empty
                            <div class="px-4 py-6 text-center text-sm text-fc-muted">{{ __('app.notifications.empty') }}</div>
                            @endforelse
                            <div class="border-t border-[var(--fc-border)] px-2 py-1.5">
                                <a href="{{ route('admin.notifications.index') }}" class="header-dropdown-item justify-center text-violet-600 font-medium">
                                    {{ __('app.notifications.view_all') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="hidden sm:block w-px h-6 bg-[var(--fc-border)]"></div>

                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" @click="open = !open"
                            class="header-profile {{ request()->routeIs('admin.profile.*') ? 'ring-2 ring-violet-500/30' : '' }}">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-purple-700 flex items-center justify-center text-white font-semibold text-sm shrink-0">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="hidden md:block text-sm font-medium text-fc max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                            <svg class="hidden sm:block w-4 h-4 text-fc-muted shrink-0 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" x-transition
                            class="header-dropdown absolute right-0 mt-2 w-[min(13rem,calc(100vw-1.5rem))] py-1.5 z-50"
                            style="display: none;">
                            <div class="px-4 py-2.5 border-b border-[var(--fc-border)]">
                                <div class="text-sm font-semibold text-fc truncate">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-fc-muted truncate">{{ auth()->user()->email }}</div>
                                <div class="text-sm font-medium text-fc truncate mt-0.5">{{ auth()->user()->roleLabel() }}</div>
                            </div>
                            <a href="{{ route('admin.profile.edit') }}" class="header-dropdown-item">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ __('app.nav.profile') }}
                            </a>
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="header-dropdown-item header-dropdown-danger w-full">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    {{ __('app.nav.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 min-w-0">
                @yield('content')
            </main>
        </div>
    </div>
    @else
    <div class="fixed top-4 right-4 z-50">
        <x-locale-theme />
    </div>
    @yield('content')
    @endauth

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition class="toast">
        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
</body>
</html>
