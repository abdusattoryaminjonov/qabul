@extends('admin.layout')

@section('title', __('app.forms.create'))

@section('content')
@php
    $formTypeMeta = [
        'survey' => [
            'theme' => 'violet',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>',
        ],
        'registration' => [
            'theme' => 'emerald',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>',
        ],
        'quiz' => [
            'theme' => 'amber',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>',
        ],
        'psychology' => [
            'theme' => 'sky',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>',
        ],
    ];
@endphp

<div class="form-create-page p-4 sm:p-6 lg:p-8 max-w-6xl mx-auto">
    <a href="{{ route('admin.forms.index') }}" class="form-create-back">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        {{ __('app.common.back') }}
    </a>

    <header class="form-create-hero">
        <p class="form-create-eyebrow">{{ __('app.nav.new_form') }}</p>
        <h1 class="form-create-title">{{ __('app.forms.create') }}</h1>
        <p class="form-create-subtitle">{{ __('app.forms.create_subtitle') }}</p>
    </header>

    <div class="form-create-shell">
        @if ($errors->any())
            <div class="form-create-errors">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.forms.store') }}">
            @csrf

            <div class="form-create-layout">
                <section class="form-create-panel">
                    <div class="form-create-panel-head">
                        <span class="form-create-step">1</span>
                        <div>
                            <h2 class="form-create-panel-title">{{ __('app.forms.basic_info') }}</h2>
                            <p class="form-create-panel-desc">{{ __('app.forms.basic_info_desc') }}</p>
                        </div>
                    </div>

                    <div class="form-create-fields">
                        <div>
                            <label class="form-create-label">{{ __('app.forms.form_name') }} *</label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                class="input form-create-input"
                                placeholder="{{ __('app.forms.form_name_placeholder') }}">
                            @error('title')<p class="form-create-field-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-create-label">{{ __('app.forms.description') }}</label>
                            <textarea name="description" rows="3" class="input form-create-input resize-none"
                                placeholder="{{ __('app.forms.description_placeholder') }}">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </section>

                <section class="form-create-panel">
                    <div class="form-create-panel-head">
                        <span class="form-create-step">2</span>
                        <div>
                            <h2 class="form-create-panel-title">{{ __('app.forms.choose_type') }}</h2>
                            <p class="form-create-panel-desc">{{ __('app.forms.choose_type_desc') }}</p>
                        </div>
                    </div>

                    <div class="form-create-types">
                        @foreach(\App\Models\Form::formTypeLabels() as $type => $label)
                        @php $meta = $formTypeMeta[$type] ?? ['theme' => 'violet', 'icon' => '']; @endphp
                        <label class="form-type-option form-type-option--{{ $meta['theme'] }}">
                            <input type="radio" name="form_type" value="{{ $type }}"
                                class="form-type-radio-input sr-only"
                                @checked(old('form_type', 'survey') === $type)>
                            <span class="form-type-option-badge">{{ __('app.forms.type_selected') }}</span>
                            <span class="form-type-option-check" aria-hidden="true">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span class="form-type-option-radio" aria-hidden="true"></span>
                            <span class="form-type-option-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $meta['icon'] !!}</svg>
                            </span>
                            <span class="form-type-option-body">
                                <span class="form-type-option-title">{{ $label }}</span>
                                <span class="form-type-option-desc">{{ __("app.forms.types.{$type}_desc") }}</span>
                            </span>
                        </label>
                        @endforeach
                    </div>
                    @error('form_type')<p class="form-create-field-error mt-2">{{ $message }}</p>@enderror
                </section>
            </div>

            <div class="form-create-actions">
                <a href="{{ route('admin.forms.index') }}" class="btn btn-secondary hidden sm:inline-flex">{{ __('app.common.cancel') }}</a>
                <button type="submit" class="btn btn-primary form-create-submit">
                    {{ __('app.forms.create_and_edit') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
