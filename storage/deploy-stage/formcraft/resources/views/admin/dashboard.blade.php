@extends('admin.layout')

@section('title', __('app.nav.home'))

@section('content')
<div class="p-6 lg:p-10">
    <div class="flex flex-wrap justify-between items-start gap-4 mb-8">
        <div>
            <p class="text-sm font-medium text-violet-600 mb-1">{{ __('app.nav.admin_panel') }}</p>
            <h1 class="text-2xl lg:text-3xl font-bold text-fc">{{ __('app.dashboard.greeting', ['name' => auth()->user()->name]) }}</h1>
            <p class="text-fc-muted mt-1">{{ __('app.dashboard.subtitle') }}</p>
        </div>
        <a href="{{ route('admin.forms.create') }}" class="btn btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            {{ __('app.dashboard.new_form') }}
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 {{ isset($stats['total_users']) ? 'xl:grid-cols-4' : 'xl:grid-cols-3' }} gap-4 mb-10">
        <div class="stat-card stat-card-violet">
            <div class="stat-card-blob"></div>
            <div class="stat-card-body">
                <div class="stat-card-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value">{{ number_format($stats['total_forms']) }}</div>
                    <div class="stat-card-label">{{ __('app.dashboard.total_forms') }}</div>
                </div>
            </div>
        </div>
        <div class="stat-card stat-card-emerald">
            <div class="stat-card-blob"></div>
            <div class="stat-card-body">
                <div class="stat-card-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value">{{ number_format($stats['total_responses']) }}</div>
                    <div class="stat-card-label">{{ __('app.dashboard.total_responses') }}</div>
                </div>
            </div>
        </div>
        <div class="stat-card stat-card-sky">
            <div class="stat-card-blob"></div>
            <div class="stat-card-body">
                <div class="stat-card-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value">{{ number_format($stats['active_forms']) }}</div>
                    <div class="stat-card-label">{{ __('app.dashboard.active_forms') }}</div>
                </div>
            </div>
        </div>
        @if(isset($stats['total_users']))
        <div class="stat-card stat-card-amber">
            <div class="stat-card-blob"></div>
            <div class="stat-card-body">
                <div class="stat-card-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value">{{ number_format($stats['total_users']) }}</div>
                    <div class="stat-card-label">{{ __('app.dashboard.total_users') }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="flex items-center justify-between mb-5">
        <h2 class="text-lg font-bold text-fc">{{ __('app.dashboard.recent_forms') }}</h2>
        <a href="{{ route('admin.forms.index') }}" class="text-sm text-violet-600 font-medium">{{ __('app.common.all') }} →</a>
    </div>

    @if($forms->isEmpty())
    <div class="card p-14 text-center">
        <p class="text-fc-muted mb-5">{{ __('app.dashboard.no_forms') }}</p>
        <a href="{{ route('admin.forms.create') }}" class="btn btn-primary">{{ __('app.dashboard.create_first') }}</a>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($forms as $form)
        <div class="card card-hover overflow-hidden">
            <div class="h-1.5" style="background: linear-gradient(90deg, {{ $form->theme_color }}, {{ $form->theme_color }}99)"></div>
            <div class="p-5">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <h3 class="font-semibold text-fc leading-snug">{{ $form->title }}</h3>
                    @if($form->isQuiz())<span class="badge badge-warning shrink-0">{{ __('app.common.test') }}</span>@endif
                    @if($form->isPsychologyTest())<span class="badge badge-info shrink-0">{{ __('app.common.psychology_test') }}</span>@endif
                </div>
                <p class="text-sm text-fc-muted">{{ __('app.dashboard.responses_count', ['count' => $form->responses_count]) }}</p>
                @if(auth()->user()->canViewAllForms() && $form->relationLoaded('user'))
                <p class="text-xs text-violet-600 mt-1">{{ __('app.forms.owner') }}: {{ $form->user->name }}</p>
                @endif
                <div class="flex items-center justify-end gap-0.5 mt-4 pt-4 border-t border-[var(--fc-border)]">
                    <a href="{{ $form->publicUrl() }}" target="_blank" class="action-icon" data-tooltip="{{ __('app.common.view') }}" aria-label="{{ __('app.common.view') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    @can('viewResponses', $form)
                    <a href="{{ route('admin.responses.index', $form) }}" class="action-icon" data-tooltip="{{ __('app.common.responses') }}" aria-label="{{ __('app.common.responses') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </a>
                    @endcan
                    @can('update', $form)
                    <a href="{{ route('admin.forms.edit', $form) }}" class="action-icon" data-tooltip="{{ __('app.common.edit') }}" aria-label="{{ __('app.common.edit') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
