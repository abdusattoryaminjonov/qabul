@extends('admin.layout')

@section('title', __('app.responses.analytics'))

@section('content')
@php
    $lastResponse = $form->responses()->latest('submitted_at')->first();
    $firstResponse = $form->responses()->oldest('submitted_at')->first();
@endphp
<div class="analytics-page p-6 lg:p-10 max-w-6xl mx-auto">
    {{-- Top nav --}}
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.responses.index', $form) }}" class="analytics-back-link">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('app.responses.back') }}
        </a>
        <span class="text-fc-muted/40">|</span>
        <a href="{{ route('admin.forms.edit', $form) }}" class="analytics-back-link">{{ __('app.common.edit') }}</a>
    </div>

    {{-- Hero --}}
    <div class="analytics-hero card overflow-hidden mb-8">
        <div class="analytics-hero-accent" style="background: linear-gradient(90deg, {{ $form->theme_color }}, {{ $form->theme_color }}88)"></div>
        <div class="p-6 lg:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold uppercase tracking-wide text-violet-600 mb-2">{{ __('app.responses.analytics') }}</p>
                    <h1 class="text-2xl lg:text-3xl font-bold text-fc leading-tight">{{ $form->title }}</h1>
                    <p class="text-fc-muted mt-2">{{ __('app.responses.analytics_subtitle', ['count' => $totalResponses]) }}</p>
                    <div class="flex flex-wrap gap-2 mt-4">
                        @if($form->isQuiz())<span class="badge badge-warning">{{ __('app.common.test') }}</span>@endif
                        @if($form->isPsychologyTest())<span class="badge badge-info">{{ __('app.common.psychology_test') }}</span>@endif
                        @if($form->isRegistration())<span class="badge badge-registration">{{ __('app.common.registration') }}</span>@endif
                        @if($form->is_active && $form->accept_responses)
                        <span class="badge badge-success">{{ __('app.common.active') }}</span>
                        @else
                        <span class="badge badge-muted">{{ __('app.common.closed') }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <a href="{{ route('admin.responses.index', $form) }}" class="btn btn-secondary text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        {{ __('app.responses.view_list') }}
                    </a>
                    @can('exportResponses', $form)
                    <a href="{{ route('admin.responses.export', $form) }}" class="btn btn-primary text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        {{ __('app.common.export_excel') }}
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    @if($totalResponses === 0)
    <div class="card p-14 text-center">
        <div class="analytics-empty-icon mx-auto mb-4">
            <svg class="w-10 h-10 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <p class="text-fc-muted mb-2">{{ __('app.responses.empty') }}</p>
        <p class="text-sm text-fc-muted">{{ __('app.responses.share_hint') }}</p>
        <a href="{{ $form->publicUrl() }}" target="_blank" class="text-violet-600 font-medium text-sm mt-3 inline-block">{{ $form->publicUrl() }}</a>
    </div>
    @else
    {{-- Summary stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <div class="stat-card stat-card-violet">
            <div class="stat-card-blob"></div>
            <div class="stat-card-body">
                <div class="stat-card-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value">{{ number_format($totalResponses) }}</div>
                    <div class="stat-card-label">{{ __('app.responses.total_answers') }}</div>
                </div>
            </div>
        </div>
        <div class="stat-card stat-card-emerald">
            <div class="stat-card-blob"></div>
            <div class="stat-card-body">
                <div class="stat-card-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value">{{ $form->questions->count() }}</div>
                    <div class="stat-card-label">{{ __('app.builder.questions') }}</div>
                </div>
            </div>
        </div>
        <div class="stat-card stat-card-sky">
            <div class="stat-card-blob"></div>
            <div class="stat-card-body">
                <div class="stat-card-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value">{{ round($totalResponses / max($form->questions->count(), 1), 1) }}</div>
                    <div class="stat-card-label">{{ __('app.responses.avg_per_question') }}</div>
                </div>
            </div>
        </div>
        <div class="stat-card stat-card-amber">
            <div class="stat-card-blob"></div>
            <div class="stat-card-body">
                <div class="stat-card-icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-value text-lg">{{ $lastResponse?->submitted_at?->format('d.m.Y') ?? '—' }}</div>
                    <div class="stat-card-label">{{ __('app.responses.last_response') }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($responsesOverTime->count() > 0)
    <div class="card analytics-chart-card mb-8 overflow-hidden">
        <div class="analytics-section-head">
            <div>
                <h2 class="analytics-section-title">{{ __('app.responses.timeline') }}</h2>
                @if($firstResponse && $lastResponse)
                <p class="analytics-section-desc">{{ $firstResponse->submitted_at->format('d.m.Y') }} — {{ $lastResponse->submitted_at->format('d.m.Y') }}</p>
                @endif
            </div>
            <span class="analytics-pill">{{ $responsesOverTime->sum('count') }} {{ __('app.responses.daily_total') }}</span>
        </div>
        <div class="px-4 pb-6 lg:px-6 lg:pb-8">
            <div class="analytics-chart-wrap h-64 lg:h-72"><canvas id="timeline-chart"></canvas></div>
        </div>
    </div>
    @endif

    <div class="flex items-center justify-between gap-4 mb-5">
        <h2 class="text-lg font-bold text-fc">{{ __('app.responses.question_breakdown') }}</h2>
        <span class="text-sm text-fc-muted">{{ $form->questions->count() }} {{ __('app.builder.questions') }}</span>
    </div>

    <div class="space-y-5">
        @foreach($questionStats as $index => $stat)
        @php
            $question = $stat['question'];
            $maxOptionCount = isset($stat['option_counts']) ? max(array_values($stat['option_counts'] ?: [0])) : 0;
        @endphp
        <div class="card analytics-question-card overflow-hidden">
            <div class="analytics-question-head">
                <div class="analytics-q-num">{{ $index + 1 }}</div>
                <div class="min-w-0 flex-1">
                    <h3 class="font-semibold text-fc leading-snug">{{ $question->title }}</h3>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="badge badge-brand">{{ __('app.question_types.'.$question->type) }}</span>
                        <span class="text-xs text-fc-muted">{{ __('app.responses.answers', ['count' => $stat['total']]) }}</span>
                        @if($question->is_required)<span class="text-xs text-red-500 font-medium">{{ __('app.common.required') }}</span>@endif
                    </div>
                </div>
            </div>

            <div class="p-5 lg:p-6 border-t border-[var(--fc-border)]">
                @if(isset($stat['option_counts']))
                <p class="text-xs font-semibold uppercase tracking-wide text-fc-muted mb-4">{{ __('app.responses.choice_distribution') }}</p>
                <div class="grid lg:grid-cols-2 gap-6 lg:gap-8 items-start">
                    <div class="analytics-chart-wrap h-56 lg:h-64 mx-auto w-full max-w-xs lg:max-w-none">
                        <canvas id="chart-options-{{ $question->id }}"></canvas>
                    </div>
                    <div class="space-y-4">
                        @foreach($stat['option_counts'] as $option => $count)
                        @php
                            $pct = $stat['total'] > 0 ? round($count / $stat['total'] * 100) : 0;
                            $isTop = $count > 0 && $count === $maxOptionCount;
                        @endphp
                        <div class="analytics-bar-row {{ $isTop ? 'analytics-bar-row-top' : '' }}">
                            <div class="flex justify-between gap-3 text-sm mb-2">
                                <span class="text-fc font-medium leading-snug">{{ Str::limit($option, 60) }}</span>
                                <span class="text-fc-muted shrink-0 tabular-nums">{{ $count }} · {{ $pct }}%</span>
                            </div>
                            <div class="analytics-bar-track">
                                <div class="analytics-bar-fill" style="width: {{ max($pct, $count > 0 ? 4 : 0) }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @elseif(isset($stat['scale_counts']))
                <p class="text-xs font-semibold uppercase tracking-wide text-fc-muted mb-4">{{ __('app.responses.scale_distribution') }}</p>
                <div class="analytics-chart-wrap h-64"><canvas id="chart-scale-{{ $question->id }}"></canvas></div>
                @else
                <p class="text-xs font-semibold uppercase tracking-wide text-fc-muted mb-4">{{ __('app.responses.text_responses') }} ({{ $stat['text_answers']->count() }})</p>
                <div class="analytics-text-list">
                    @forelse($stat['text_answers'] as $text)
                    <div class="analytics-text-item">{{ $text }}</div>
                    @empty
                    <p class="text-fc-muted text-sm py-4 text-center">{{ __('app.common.no_data') }}</p>
                    @endforelse
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@if($totalResponses > 0)
@push('scripts')
<script type="module">
import Chart from 'chart.js/auto';

const chartColors = ['#7c3aed', '#8b5cf6', '#a78bfa', '#c4b5fd', '#6d28d9', '#5b21b6', '#4c1d95', '#ddd6fe', '#ede9fe'];
const isDark = document.documentElement.classList.contains('dark');
const gridColor = isDark ? 'rgba(148,163,184,0.12)' : 'rgba(100,116,139,0.12)';
const textColor = isDark ? '#e2e8f0' : '#334155';

Chart.defaults.color = textColor;
Chart.defaults.borderColor = gridColor;
Chart.defaults.font.family = "'Instrument Sans', ui-sans-serif, system-ui, sans-serif";

const doughnutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '62%',
    plugins: {
        legend: {
            position: 'bottom',
            labels: { boxWidth: 12, padding: 14, font: { size: 11 } },
        },
    },
};

const barOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
        y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: gridColor } },
    },
};

@foreach($chartData as $chart)
@if($chart['type'] === 'options')
new Chart(document.getElementById('chart-options-{{ $chart['id'] }}'), {
    type: 'doughnut',
    data: {
        labels: @json($chart['labels']),
        datasets: [{ data: @json($chart['values']), backgroundColor: chartColors, borderWidth: 0, hoverOffset: 6 }]
    },
    options: doughnutOptions,
});
@elseif($chart['type'] === 'scale')
new Chart(document.getElementById('chart-scale-{{ $chart['id'] }}'), {
    type: 'bar',
    data: {
        labels: @json($chart['labels']),
        datasets: [{
            label: @json(__('app.responses.answers', ['count' => ''])),
            data: @json($chart['values']),
            backgroundColor: 'rgba(124,58,237,0.85)',
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: barOptions,
});
@endif
@endforeach

@if($responsesOverTime->count() > 0)
new Chart(document.getElementById('timeline-chart'), {
    type: 'line',
    data: {
        labels: @json($responsesOverTime->pluck('date')),
        datasets: [{
            label: @json(__('app.responses.daily')),
            data: @json($responsesOverTime->pluck('count')),
            borderColor: '#7c3aed',
            backgroundColor: 'rgba(124,58,237,0.12)',
            fill: true,
            tension: 0.35,
            pointBackgroundColor: '#7c3aed',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 11 }, maxRotation: 45 } },
            y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: gridColor } },
        },
    },
});
@endif
</script>
@endpush
@endif
@endsection
