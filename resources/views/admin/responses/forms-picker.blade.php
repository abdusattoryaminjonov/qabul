@extends('admin.layout')

@section('title', $mode === 'statistics' ? __('app.statistics.title') : __('app.responses.title'))

@section('content')
<div class="p-6 lg:p-10">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-fc">
            {{ $mode === 'statistics' ? __('app.statistics.title') : __('app.responses.title') }}
        </h1>
        <p class="text-fc-muted text-sm mt-1">
            {{ $mode === 'statistics' ? __('app.statistics.pick_form_hint') : __('app.responses.pick_form_hint') }}
        </p>
    </div>

    @if($forms->isEmpty())
    <div class="card p-14 text-center">
        <p class="text-fc-muted">{{ __('app.responses.no_forms') }}</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($forms as $form)
        @php
            $canView = auth()->user()->can('viewResponses', $form);
            $targetUrl = $mode === 'statistics'
                ? route('admin.responses.analytics', $form)
                : route('admin.responses.index', $form);
        @endphp
        @if($canView)
        <a href="{{ $targetUrl }}" class="card card-hover overflow-hidden block">
            <div class="h-1.5" style="background: linear-gradient(90deg, {{ $form->theme_color }}, {{ $form->theme_color }}99)"></div>
            <div class="p-5">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <h3 class="font-semibold text-fc leading-snug">{{ $form->title }}</h3>
                    @if($form->isQuiz())<span class="badge badge-warning shrink-0">{{ __('app.common.test') }}</span>@endif
                    @if($form->isPsychologyTest())<span class="badge badge-info shrink-0">{{ __('app.common.psychology_test') }}</span>@endif
                    @if($form->isRegistration())<span class="badge badge-registration shrink-0">{{ __('app.common.registration') }}</span>@endif
                </div>
                <p class="text-sm text-fc-muted">{{ __('app.responses.count', ['count' => $form->responses_count]) }}</p>
                @if($form->relationLoaded('user') && (auth()->user()->isSuperAdmin() || auth()->user()->canViewAnyResponses()))
                <p class="text-xs text-violet-600 mt-1">{{ __('app.forms.owner') }}: {{ $form->user->name ?? '—' }}</p>
                @endif
                <div class="flex items-center gap-1.5 mt-4 pt-4 border-t border-[var(--fc-border)] text-sm font-medium text-violet-600">
                    @if($mode === 'statistics')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    {{ __('app.statistics.view') }}
                    @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    {{ __('app.responses.view_list') }}
                    @endif
                </div>
            </div>
        </a>
        @endif
        @endforeach
    </div>
    <div class="mt-6">{{ $forms->links() }}</div>
    @endif
</div>
@endsection
