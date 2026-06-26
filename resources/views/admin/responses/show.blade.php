@extends('admin.layout')

@section('title', __('app.responses.detail', ['id' => $response->id]))

@section('content')
<div class="p-6 lg:p-10 max-w-6xl mx-auto">
    <a href="{{ route('admin.responses.index', $form) }}" class="inline-flex items-center gap-1.5 text-sm text-fc-muted hover:text-violet-600 font-medium mb-6">← {{ __('app.responses.back') }}</a>
    <div class="card overflow-hidden">
        <div class="p-6 lg:p-8 border-b border-[var(--fc-border)] flex justify-between items-start gap-4 bg-violet-500/5">
            <div>
                <p class="text-sm font-medium text-violet-600 mb-1">{{ __('app.responses.detail', ['id' => $response->id]) }}</p>
                <p class="text-fc-muted text-sm">{{ $response->submitted_at->format('d.m.Y, H:i') }}</p>
                @if($response->respondent_email)<p class="text-fc text-sm mt-1 font-medium">{{ $response->respondent_email }}</p>@endif
            </div>
            @if($form->isQuiz())
            <div class="text-center card px-6 py-4">
                <div class="text-3xl font-bold text-violet-600">{{ $response->score }}/{{ $response->max_score }}</div>
                <div class="text-xs text-fc-muted mt-1 font-medium">{{ __('app.common.points') }}</div>
            </div>
            @elseif($form->isPsychologyTest())
            <div class="text-center card px-6 py-4">
                <div class="text-3xl font-bold text-sky-600">{{ $response->score }}</div>
                <div class="text-xs text-fc-muted mt-1 font-medium">{{ __('app.public.psych_total_score') }}</div>
                @if($response->result_data['interpretation']['title'] ?? null)
                <div class="text-sm font-semibold text-fc mt-2">{{ $response->result_data['interpretation']['title'] }}</div>
                @endif
            </div>
            @endif
        </div>
        <div class="p-6 lg:p-8 grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-6 response-answers-grid">
            @foreach($form->questions as $question)
            @php $answer = $response->answers->firstWhere('question_id', $question->id); @endphp
            <div>
                <div class="text-sm font-semibold text-fc mb-2">{{ $question->title }}</div>
                <div class="bg-[var(--fc-hover)] rounded-xl px-4 py-3.5 text-fc text-sm border border-[var(--fc-border)]">
                    @include('admin.responses.partials.answer-display', [
                        'form' => $form,
                        'response' => $response,
                        'answer' => $answer,
                    ])
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
