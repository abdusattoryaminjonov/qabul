@extends('forms.layout')

@section('title', __('app.public.thank_you'))

@push('head')
<style>:root { --form-theme: {{ $form->theme_color }}; }</style>
@endpush

@section('body')
<div class="public-form {{ $form->isPsychologyTest() && $response ? 'max-w-2xl' : 'max-w-md' }} w-full mx-auto px-4 py-8">
    <div class="public-result-card text-center">
        <div class="public-result-icon">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="text-2xl lg:text-3xl font-bold text-fc mb-3">{{ __('app.public.thank_you') }}</h1>
        <p class="text-fc-muted leading-relaxed">{{ $form->localizedConfirmation() ?: __('app.public.thank_you_default') }}</p>

        @if($score !== null && $max_score !== null)
        <div class="public-score-box">
            <div class="public-score-value">{{ $score }}/{{ $max_score }}</div>
            <div class="text-sm text-fc-muted mt-2 font-medium">{{ __('app.public.your_score') }}</div>
        </div>
        @endif

        @if($form->isPsychologyTest() && $response)
        <div class="public-psych-results text-left mt-8 space-y-6">
            <div class="public-psych-score text-center">
                <div class="text-sm font-medium text-fc-muted uppercase tracking-wide">{{ __('app.public.psych_total_score') }}</div>
                <div class="public-score-value mt-1">{{ $response->score }}</div>
            </div>

            @if($response->result_data['interpretation'] ?? null)
            @php $interpretation = $response->result_data['interpretation']; @endphp
            <div class="public-psych-interpretation">
                <h2 class="text-lg font-bold text-fc mb-2">{{ $interpretation['title'] }}</h2>
                <p class="text-fc-muted leading-relaxed">{{ $interpretation['description'] }}</p>
            </div>
            @endif

            @if(count($answerRows))
            <div>
                <h3 class="text-base font-bold text-fc mb-4">{{ __('app.public.your_answers') }}</h3>
                <div class="space-y-3">
                    @foreach($answerRows as $row)
                    <div class="public-psych-answer-row">
                        <div class="text-sm font-semibold text-fc mb-1">{{ $row['question'] }}</div>
                        <div class="flex items-start justify-between gap-3">
                            <div class="text-sm text-fc-muted">{{ $row['answer'] ?: '—' }}</div>
                            @if($row['points'] !== null)
                            <span class="public-psych-points shrink-0">+{{ $row['points'] }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
