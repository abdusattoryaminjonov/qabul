@extends('admin.layout')

@section('title', __('app.responses.analytics'))

@section('content')
<div class="p-6 lg:p-10 max-w-5xl mx-auto">
    <a href="{{ route('admin.statistics.forms') }}" class="inline-flex items-center gap-1.5 text-sm text-fc-muted hover:text-violet-600 font-medium mb-4">← {{ __('app.statistics.back') }}</a>
    <h1 class="text-2xl font-bold text-fc">{{ __('app.responses.analytics') }}</h1>
    <p class="text-fc-muted mb-8">{{ __('app.responses.analytics_subtitle', ['count' => $totalResponses]) }}</p>

    @if($totalResponses === 0)
    <div class="card p-14 text-center text-fc-muted">{{ __('app.responses.empty') }}</div>
    @else
    @if($responsesOverTime->count() > 1)
    <div class="card p-6 mb-6">
        <h3 class="font-semibold text-fc mb-4">{{ __('app.responses.timeline') }}</h3>
        <div class="h-64"><canvas id="timeline-chart"></canvas></div>
    </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="stat-card violet">
            <div class="text-3xl font-bold text-fc">{{ $totalResponses }}</div>
            <div class="text-fc-muted text-sm mt-1">{{ __('app.responses.total_answers') }}</div>
        </div>
        <div class="stat-card emerald">
            <div class="text-3xl font-bold text-fc">{{ $form->questions->count() }}</div>
            <div class="text-fc-muted text-sm mt-1">{{ __('app.builder.questions') }}</div>
        </div>
        <div class="stat-card sky">
            <div class="text-3xl font-bold text-fc">{{ round($totalResponses / max($form->questions->count(), 1), 1) }}</div>
            <div class="text-fc-muted text-sm mt-1">{{ __('app.responses.avg_per_question') }}</div>
        </div>
    </div>

    <div class="space-y-6">
        @foreach($questionStats as $index => $stat)
        <div class="card p-6">
            <h3 class="font-semibold text-fc">{{ $stat['question']->title }}</h3>
            <p class="text-xs text-fc-muted mt-1 mb-5">{{ __('app.responses.answers', ['count' => $stat['total']]) }} · {{ __('app.question_types.'.$stat['question']->type) }}</p>

            @if(isset($stat['option_counts']))
            <div class="grid lg:grid-cols-2 gap-6 items-center">
                <div class="h-56"><canvas id="chart-options-{{ $stat['question']->id }}"></canvas></div>
                <div class="space-y-3">
                    @foreach($stat['option_counts'] as $option => $count)
                    @php $pct = $stat['total'] > 0 ? round($count / $stat['total'] * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1.5"><span class="text-fc font-medium">{{ $option }}</span><span class="text-fc-muted">{{ $count }} ({{ $pct }}%)</span></div>
                        <div class="h-2.5 bg-[var(--fc-hover)] rounded-full overflow-hidden"><div class="h-full rounded-full bg-gradient-to-r from-violet-600 to-violet-400" style="width:{{ $pct }}%"></div></div>
                    </div>
                    @endforeach
                </div>
            </div>
            @elseif(isset($stat['scale_counts']))
            <div class="h-64"><canvas id="chart-scale-{{ $stat['question']->id }}"></canvas></div>
            @else
            <div class="space-y-2 max-h-52 overflow-y-auto">
                @forelse($stat['text_answers'] as $text)
                <div class="text-sm bg-[var(--fc-hover)] rounded-xl px-4 py-2.5 text-fc border border-[var(--fc-border)]">{{ $text }}</div>
                @empty
                <p class="text-fc-muted text-sm">{{ __('app.common.no_data') }}</p>
                @endforelse
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

@if($totalResponses > 0)
@push('scripts')
<script type="module">
import Chart from 'chart.js/auto';

const chartColors = ['#7c3aed','#a78bfa','#c4b5fd','#8b5cf6','#6d28d9','#5b21b6','#4c1d95','#ddd6fe'];
const isDark = document.documentElement.classList.contains('dark');
const gridColor = isDark ? 'rgba(148,163,184,0.15)' : 'rgba(100,116,139,0.15)';
const textColor = isDark ? '#e2e8f0' : '#334155';

Chart.defaults.color = textColor;
Chart.defaults.borderColor = gridColor;

@foreach($chartData as $chart)
@if($chart['type'] === 'options')
new Chart(document.getElementById('chart-options-{{ $chart['id'] }}'), {
    type: 'doughnut',
    data: {
        labels: @json($chart['labels']),
        datasets: [{ data: @json($chart['values']), backgroundColor: chartColors, borderWidth: 0 }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});
@elseif($chart['type'] === 'scale')
new Chart(document.getElementById('chart-scale-{{ $chart['id'] }}'), {
    type: 'bar',
    data: {
        labels: @json($chart['labels']),
        datasets: [{ label: @json(__('app.responses.answers', ['count' => ''])), data: @json($chart['values']), backgroundColor: 'rgba(124,58,237,0.8)', borderRadius: 8 }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
@endif
@endforeach

@if($responsesOverTime->count() > 1)
new Chart(document.getElementById('timeline-chart'), {
    type: 'line',
    data: {
        labels: @json($responsesOverTime->pluck('date')),
        datasets: [{ label: @json(__('app.responses.daily')), data: @json($responsesOverTime->pluck('count')), borderColor: '#7c3aed', backgroundColor: 'rgba(124,58,237,0.1)', fill: true, tension: 0.35 }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
@endif
</script>
@endpush
@endif
@endsection
