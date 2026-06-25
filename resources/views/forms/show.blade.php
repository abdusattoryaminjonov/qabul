@extends('forms.layout')

@section('title', $form->title)

@push('head')
<style>:root { --form-theme: {{ $form->theme_color }}; }</style>
@endpush

@section('body')
<div class="public-form max-w-2xl mx-auto px-4 pb-12">
    @if($form->show_progress_bar && $questions->count() > 0)
    <div class="public-progress mb-6">
        <div class="flex justify-between text-xs font-medium text-fc-muted mb-2">
            <span>{{ __('app.public.progress') }}</span>
            <span id="progress-text">0%</span>
        </div>
        <div class="public-progress-track">
            <div id="progress-bar" class="public-progress-fill"></div>
        </div>
    </div>
    @endif

    <form action="{{ route('forms.submit', $form->slug) }}" method="POST" enctype="multipart/form-data" class="public-form-inner">
        @csrf

        <header class="public-form-hero">
            <div class="public-form-hero-accent"></div>
            <div class="public-form-hero-body">
                <h1 class="public-form-title">{{ $form->localizedTitle() }}</h1>
                @if($form->localizedDescription())
                <p class="public-form-desc">{{ $form->localizedDescription() }}</p>
                @endif
                @if($form->isQuiz())
                <span class="public-form-badge">{{ __('app.public.test_points', ['count' => $questions->sum('points') ?: $questions->whereIn('type', ['multiple_choice','checkbox','dropdown'])->count()]) }}</span>
                @elseif($form->isPsychologyTest())
                <span class="public-form-badge public-form-badge-psych">{{ __('app.common.psychology_test') }}</span>
                @elseif($form->isRegistration())
                <span class="public-form-badge public-form-badge-registration">{{ __('app.common.registration') }}</span>
                @endif
            </div>
        </header>

        @if($errors->any())
        <div class="public-form-error">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div>
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        </div>
        @endif

        <div class="public-form-fields space-y-4">
            @if($form->collect_email)
            <div class="public-field-card">
                <label class="public-field-label">
                    {{ __('app.public.email_required') }}
                    <span class="text-red-500">*</span>
                </label>
                <input type="email" name="respondent_email" value="{{ old('respondent_email') }}" required class="input" placeholder="email@example.com">
            </div>
            @endif

            @foreach($questions as $index => $question)
            <div class="public-field-card question-block" data-index="{{ $index }}">
                <div class="flex gap-4">
                    <div class="public-q-num">{{ $index + 1 }}</div>
                    <div class="flex-1 min-w-0">
                        <label class="public-field-label">
                            {{ $question->localizedTitle() }}
                            @if($question->is_required)<span class="text-red-500 ml-0.5">*</span>@endif
                        </label>
                        @if($question->localizedDescription())
                        <p class="public-field-hint">{{ $question->localizedDescription() }}</p>
                        @endif
                        <div class="mt-3">
                            @include('forms.partials.question-input', ['question' => $question, 'form' => $form])
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="public-form-submit">
            <button type="submit" class="public-submit-btn">
                {{ __('app.public.submit') }}
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </div>
    </form>
</div>

@if($form->show_progress_bar)
<script>
    const blocks = document.querySelectorAll('.question-block');
    const bar = document.getElementById('progress-bar');
    const text = document.getElementById('progress-text');
    if (blocks.length && bar) {
        const update = () => {
            const scrolled = [...blocks].filter(b => b.getBoundingClientRect().top < window.innerHeight * 0.75).length;
            const pct = Math.min(100, Math.round(scrolled / blocks.length * 100));
            bar.style.width = pct + '%';
            if (text) text.textContent = pct + '%';
        };
        window.addEventListener('scroll', update, { passive: true });
        update();
    }
</script>
@endif
@endsection
