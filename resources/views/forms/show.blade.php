@extends('forms.layout')

@section('title', $form->title)

@push('head')
<style>:root { --form-theme: #2dd4bf; }</style>
@endpush

@section('body')
@if($form->show_progress_bar && $questions->count() > 0)
<div class="public-progress-sticky" aria-hidden="true">
    <div id="progress-bar" class="public-progress-sticky-fill"></div>
</div>
@endif

<div class="public-shell-wrap">
    <form action="{{ route('forms.submit', $form->slug) }}" method="POST" enctype="multipart/form-data" class="public-shell">
        @csrf

        <header class="public-shell-header">
            <div class="public-shell-header-glow" aria-hidden="true"></div>
            <div class="public-shell-header-inner">
                @if($form->isQuiz())
                <span class="public-shell-badge">{{ __('app.public.test_points', ['count' => $questions->sum('points') ?: $questions->whereIn('type', ['multiple_choice','checkbox','dropdown'])->count()]) }}</span>
                @elseif($form->isPsychologyTest())
                <span class="public-shell-badge public-shell-badge-psych">{{ __('app.common.psychology_test') }}</span>
                @elseif($form->isRegistration())
                <span class="public-shell-badge public-shell-badge-registration">{{ __('app.common.registration') }}</span>
                @else
                <span class="public-shell-badge public-shell-badge-survey">{{ __('app.forms.types.survey') }}</span>
                @endif

                <h1 class="public-shell-title">{{ $form->localizedTitle() }}</h1>

                @if($form->localizedDescription())
                <p class="public-shell-desc">{{ $form->localizedDescription() }}</p>
                @endif

                @if($questions->count() > 0)
                <div class="public-shell-meta">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>{{ __('app.public.questions_count', ['count' => $questions->count()]) }}</span>
                    @if($form->show_progress_bar)
                    <span class="public-shell-meta-dot">·</span>
                    <span id="progress-text">0%</span>
                    @endif
                </div>
                @endif
            </div>
        </header>

        <div class="public-shell-body">
            @if($errors->any())
            <div class="public-form-error" role="alert">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p>{{ __('app.public.fill_required_hint') }}</p>
            </div>
            @endif

            @php
                $firstFieldErrorAssigned = false;
                $emailError = $form->collect_email && \App\Support\FormSubmissionRules::fieldHasError($errors, 'respondent_email');
                if ($emailError) {
                    $firstFieldErrorAssigned = true;
                }
            @endphp

            @if($form->collect_email)
            <div class="public-field-card @if($emailError) public-field-card--error @endif" @if($emailError) id="first-field-error" @endif>
                <div class="public-field-accent" aria-hidden="true"></div>
                <div class="public-field-inner">
                    <label class="public-field-label">
                        {{ __('app.public.email_required') }}
                        <span class="public-required">*</span>
                    </label>
                    <input type="email" name="respondent_email" value="{{ old('respondent_email') }}" required class="input public-input @if($emailError) public-input--error @endif" placeholder="email@example.com">
                    @if($emailError)
                    <p class="public-field-error-msg">{{ __('app.public.required_field') }}</p>
                    @endif
                </div>
            </div>
            @endif

            @foreach($questions as $index => $question)
            @php
                $fieldError = \App\Support\FormSubmissionRules::fieldHasError($errors, 'answers.'.$question->id);
                $isFirstError = $fieldError && ! $firstFieldErrorAssigned;
                if ($isFirstError) {
                    $firstFieldErrorAssigned = true;
                }
            @endphp
            <div class="public-field-card question-block @if($fieldError) public-field-card--error @endif" @if($isFirstError) id="first-field-error" @endif data-index="{{ $index }}">
                <div class="public-field-accent" aria-hidden="true"></div>
                <div class="public-field-inner">
                    <div class="public-field-head">
                        <span class="public-q-num @if($fieldError) public-q-num--error @endif">{{ $index + 1 }}</span>
                        <div class="public-field-copy">
                            <label class="public-field-label">
                                {{ $question->localizedTitle() }}
                                @if($question->is_required)<span class="public-required">*</span>@endif
                            </label>
                            @if($question->localizedDescription())
                            <p class="public-field-hint">{{ $question->localizedDescription() }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="public-field-input">
                        @include('forms.partials.question-input', ['question' => $question, 'form' => $form, 'fieldError' => $fieldError])
                    </div>
                    @if($fieldError)
                    <p class="public-field-error-msg">{{ __('app.public.required_field') }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <footer class="public-shell-footer">
            <button type="submit" class="public-submit-btn">
                {{ __('app.public.submit') }}
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </footer>
    </form>
</div>

@if($errors->any())
<script>
    document.getElementById('first-field-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
</script>
@endif

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
