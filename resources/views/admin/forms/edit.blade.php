@extends('admin.layout')

@section('title', $form->title)

@section('content')
<div x-data="formBuilder()" x-init="init()" class="min-h-screen">
    {{-- Toolbar --}}
    <div class="builder-toolbar sticky top-14 z-30">
        <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('admin.forms.index') }}" class="btn btn-ghost py-2 px-2 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <input type="text" x-model="formTitle"
                    class="text-base font-bold border-0 border-b-2 border-transparent hover:border-slate-200 focus:border-violet-500 outline-none bg-transparent truncate text-slate-900">
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span x-show="saved" x-transition class="text-xs font-medium text-emerald-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ __('app.common.saved') }}
                </span>
                <span x-show="saving" class="text-xs text-fc-muted">{{ __('app.common.saving') }}</span>
                <button @click="saveStructure()" class="btn btn-primary text-xs py-2">{{ __('app.common.save') }}</button>
                <a href="{{ $form->publicUrl() }}" target="_blank" class="btn btn-secondary text-xs py-2 hidden sm:inline-flex">{{ __('app.common.view') }}</a>
                @can('viewResponses', $form)
                <a href="{{ route('admin.responses.index', $form) }}" class="btn btn-secondary text-xs py-2 hidden md:inline-flex" title="{{ __('app.common.responses') }}">
                    {{ __('app.common.responses') }} ({{ $form->responseCount() }})
                </a>
                @endcan
            </div>
        </div>
        <div class="max-w-3xl mx-auto px-4 flex gap-1 border-t border-slate-100">
            <button @click="tab = 'questions'" :class="tab === 'questions' ? 'text-violet-600 border-violet-600' : 'text-fc-muted border-transparent'" class="px-4 py-2.5 text-sm font-semibold border-b-2 transition">{{ __('app.builder.questions') }}</button>
            <button @click="tab = 'settings'" :class="tab === 'settings' ? 'text-violet-600 border-violet-600' : 'text-fc-muted border-transparent'" class="px-4 py-2.5 text-sm font-semibold border-b-2 transition">{{ __('app.builder.settings') }}</button>
            <button @click="tab = 'share'" :class="tab === 'share' ? 'text-violet-600 border-violet-600' : 'text-fc-muted border-transparent'" class="px-4 py-2.5 text-sm font-semibold border-b-2 transition">{{ __('app.builder.share_tab') }}</button>
            @can('viewResponses', $form)
            <a href="{{ route('admin.responses.analytics', $form) }}" class="px-4 py-2.5 text-sm font-medium text-fc-muted hover:text-violet-600 border-b-2 border-transparent" title="{{ __('app.common.statistics') }}">{{ __('app.common.statistics') }}</a>
            @endcan
        </div>
    </div>

    {{-- Questions --}}
    <div x-show="tab === 'questions'" class="builder-canvas py-8">
        <div class="max-w-4xl mx-auto px-4">
            <div class="builder-locale-bar flex flex-wrap items-center gap-2 mb-4 p-3 rounded-xl bg-violet-50 border border-violet-200">
                <span class="text-xs font-semibold text-fc-muted uppercase tracking-wide mr-1">{{ __('app.builder.content_language') }}:</span>
                @foreach(\App\Support\FormTranslations::labels() as $code => $label)
                <button type="button" @click="builderLocale = '{{ $code }}'"
                    :class="builderLocale === '{{ $code }}' ? 'bg-violet-600 text-white border-violet-600 shadow-sm' : 'bg-white text-fc-muted border-[var(--fc-border)] hover:border-violet-300'"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold border transition">{{ $label }}</button>
                @endforeach
                <span class="text-xs text-violet-700 font-medium ml-auto" x-show="builderLocale === 'uz'">{{ __('app.builder.editing_uz') }}</span>
                <span class="text-xs text-violet-700 font-medium ml-auto" x-show="builderLocale === 'ru'">{{ __('app.builder.editing_ru') }}</span>
                <span class="text-xs text-violet-700 font-medium ml-auto" x-show="builderLocale === 'en'">{{ __('app.builder.editing_en') }}</span>
            </div>
            <p class="text-xs text-fc-muted mb-4" x-text="i18n.locale_hint"></p>
        </div>
        <div class="max-w-4xl mx-auto px-4 space-y-4">
        {{-- Header card --}}
        <div class="form-card overflow-hidden">
            <div class="form-card-accent" :style="'background: linear-gradient(90deg,' + themeColor + ',' + themeColor + '88)'"></div>
            <div class="p-6 lg:p-8">
                <input type="text" x-show="builderLocale === 'uz'" x-model="formTitle" placeholder="{{ __('app.builder.form_title') }}"
                    class="w-full text-2xl lg:text-3xl font-normal border-0 border-b-2 border-[var(--fc-border)] focus:border-violet-400 outline-none pb-3 mb-3 text-fc">
                <input type="text" x-show="builderLocale === 'ru'" x-model="formTranslations.ru.title" placeholder="{{ __('app.builder.form_title') }} (RU)"
                    class="w-full text-2xl lg:text-3xl font-normal border-0 border-b-2 border-[var(--fc-border)] focus:border-violet-400 outline-none pb-3 mb-3 text-fc">
                <input type="text" x-show="builderLocale === 'en'" x-model="formTranslations.en.title" placeholder="{{ __('app.builder.form_title') }} (EN)"
                    class="w-full text-2xl lg:text-3xl font-normal border-0 border-b-2 border-[var(--fc-border)] focus:border-violet-400 outline-none pb-3 mb-3 text-fc">
                <textarea x-show="builderLocale === 'uz'" x-model="formDescription" placeholder="{{ __('app.builder.form_description') }}" rows="2"
                    class="w-full border-0 outline-none text-fc-muted resize-none text-base bg-transparent"></textarea>
                <textarea x-show="builderLocale === 'ru'" x-model="formTranslations.ru.description" placeholder="{{ __('app.builder.form_description') }} (RU)" rows="2"
                    class="w-full border-0 outline-none text-fc-muted resize-none text-base bg-transparent"></textarea>
                <textarea x-show="builderLocale === 'en'" x-model="formTranslations.en.description" placeholder="{{ __('app.builder.form_description') }} (EN)" rows="2"
                    class="w-full border-0 outline-none text-fc-muted resize-none text-base bg-transparent"></textarea>
            </div>
        </div>

        {{-- Sections & Questions --}}
        <div id="questions-list" class="space-y-4">
            <template x-if="isPsychology">
                <div class="p-4 rounded-xl bg-sky-50 border border-sky-200 text-sm text-sky-900">
                    <p class="font-semibold mb-1">{{ __('app.builder.psych_help_title') }}</p>
                    <p class="text-sky-800/90 leading-relaxed">{{ __('app.builder.psych_help_text') }}</p>
                </div>
            </template>
            <template x-for="(question, qIndex) in questions" :key="question.uid">
                <div class="question-builder-card question-card" :data-uid="question.uid">
                    <div class="builder-question-head">
                        <div class="drag-handle builder-drag-handle" title="{{ __('app.builder.drag_hint') }}">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M7 2a2 2 0 11.001 4.001A2 2 0 017 2zm0 6a2 2 0 11.001 4.001A2 2 0 017 8zm0 6a2 2 0 11.001 4.001A2 2 0 017 14zm6-8a2 2 0 11-.001-4.001A2 2 0 0113 6zm0 2a2 2 0 11.001 4.001A2 2 0 0113 8zm0 6a2 2 0 11.001 4.001A2 2 0 0113 14z"/></svg>
                        </div>
                        <div class="builder-question-main">
                            <label class="builder-question-label">{{ __('app.builder.question_text_label') }} *</label>
                            <textarea x-show="builderLocale === 'uz'" x-model="question.title" rows="2" placeholder="{{ __('app.builder.question_text') }}"
                                class="builder-question-title"></textarea>
                            <textarea x-show="builderLocale === 'ru'" x-model="question.translations.ru.title" rows="2" placeholder="{{ __('app.builder.question_text') }} (RU)"
                                class="builder-question-title"></textarea>
                            <textarea x-show="builderLocale === 'en'" x-model="question.translations.en.title" rows="2" placeholder="{{ __('app.builder.question_text') }} (EN)"
                                class="builder-question-title"></textarea>
                        </div>
                        <div class="builder-question-type">
                            <label class="builder-type-label">{{ __('app.builder.question_type') }}</label>
                            <select x-model="question.type" @change="onTypeChange(question)" class="builder-type-select">
                                @foreach(\App\Models\FormQuestion::typeLabels() as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="p-4">
                        <input type="text" x-show="builderLocale === 'uz'" x-model="question.description" placeholder="{{ __('app.builder.help_text') }}"
                            class="w-full text-sm text-gray-500 border-0 outline-none mb-4">
                        <input type="text" x-show="builderLocale === 'ru'" x-model="question.translations.ru.description" placeholder="{{ __('app.builder.help_text') }} (RU)"
                            class="w-full text-sm text-gray-500 border-0 outline-none mb-4">
                        <input type="text" x-show="builderLocale === 'en'" x-model="question.translations.en.description" placeholder="{{ __('app.builder.help_text') }} (EN)"
                            class="w-full text-sm text-gray-500 border-0 outline-none mb-4">

                        {{-- Options for choice types --}}
                        <template x-if="hasOptions(question.type)">
                            <div class="space-y-2 ml-2">
                                <template x-for="(opt, oIndex) in question.options" :key="opt.uid">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-400 w-5" x-text="question.type === 'checkbox' ? '☐' : (question.type === 'dropdown' ? (oIndex+1)+'.' : '○')"></span>
                                        <input type="text" x-show="builderLocale === 'uz'" x-model="opt.text" :placeholder="i18n.option"
                                            class="flex-1 border-0 border-b border-gray-200 focus:border-indigo-400 outline-none py-1">
                                        <input type="text" x-show="builderLocale === 'ru'" x-model="opt.translations.ru" :placeholder="i18n.option_ru"
                                            class="flex-1 border-0 border-b border-gray-200 focus:border-indigo-400 outline-none py-1">
                                        <input type="text" x-show="builderLocale === 'en'" x-model="opt.translations.en" :placeholder="i18n.option_en"
                                            class="flex-1 border-0 border-b border-gray-200 focus:border-indigo-400 outline-none py-1">
                                        <template x-if="isQuiz">
                                            <label class="flex items-center gap-1 text-xs text-green-600 shrink-0">
                                                <input type="checkbox" x-model="opt.is_correct" class="rounded text-green-600">
                                                <span x-text="i18n.correct"></span>
                                            </label>
                                        </template>
                                        <template x-if="isPsychology && hasOptions(question.type)">
                                            <label class="flex items-center gap-1 text-xs text-sky-600 shrink-0">
                                                <span x-text="i18n.score_value"></span>
                                                <input type="number" x-model.number="opt.score_value" min="0" class="w-14 border rounded px-1.5 py-0.5 text-xs">
                                            </label>
                                        </template>
                                        <button @click="removeOption(question, oIndex)" class="text-gray-400 hover:text-red-500 text-lg">×</button>
                                    </div>
                                </template>
                                <button @click="addOption(question)" class="text-sm text-violet-600 font-medium hover:text-violet-700 ml-7" x-text="'+ ' + i18n.add_option"></button>
                            </div>
                        </template>

                        {{-- Linear scale --}}
                        <template x-if="question.type === 'linear_scale'">
                            <div class="flex items-center gap-4 ml-2">
                                <input type="number" x-model.number="question.settings.min" min="0" max="10" class="w-16 border rounded px-2 py-1 text-sm">
                                <span class="text-gray-400">—</span>
                                <input type="number" x-model.number="question.settings.max" min="1" max="10" class="w-16 border rounded px-2 py-1 text-sm">
                                <input type="text" x-model="question.settings.min_label" :placeholder="i18n.min_label" class="flex-1 input text-sm py-1">
                                <input type="text" x-model="question.settings.max_label" :placeholder="i18n.max_label" class="flex-1 input text-sm py-1">
                            </div>
                        </template>

                        {{-- Preview for other types --}}
                        <template x-if="!hasOptions(question.type) && question.type !== 'linear_scale'">
                            <div class="ml-2 text-gray-400 text-sm italic" x-text="typePreview(question.type)"></div>
                        </template>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3 border-t border-slate-100 bg-slate-50/50 rounded-b-xl">
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                                <input type="checkbox" x-model="question.is_required" class="rounded text-violet-600">
                                {{ __('app.common.required') }}
                            </label>
                            <template x-if="isQuiz && hasOptions(question.type)">
                                <div class="flex items-center gap-1 text-sm">
                                    <span class="text-fc-muted">{{ __('app.common.points') }}:</span>
                                    <input type="number" x-model.number="question.points" min="0" class="w-14 border rounded px-2 py-0.5">
                                </div>
                            </template>
                            <template x-if="isPsychology && question.type === 'linear_scale'">
                                <span class="text-xs text-sky-600">{{ __('app.builder.psych_scale_hint') }}</span>
                            </template>
                        </div>
                        <button @click="removeQuestion(qIndex)" class="btn btn-danger text-xs py-1.5 px-2">{{ __('app.builder.delete_question_btn') }}</button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Add question --}}
        <div class="card border-2 border-dashed border-violet-200 bg-violet-50/30 p-6">
            <p class="text-sm font-semibold text-fc-muted mb-4 text-center">{{ __('app.builder.add_question') }}</p>
            <div class="mb-4 flex flex-col items-center gap-1.5">
                <button @click="addConsentCheckbox()" type="button"
                    class="inline-flex items-center gap-2 text-sm font-semibold bg-emerald-600 text-white border-2 border-emerald-600 rounded-xl px-5 py-2.5 hover:bg-emerald-700 hover:border-emerald-700 transition shadow-md">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('app.builder.add_single_checkbox') }}
                </button>
                <p class="text-xs text-fc-muted text-center max-w-md">{{ __('app.builder.add_single_checkbox_hint') }}</p>
            </div>
            <div class="flex flex-wrap justify-center gap-2">
                @foreach(\App\Models\FormQuestion::typeLabels() as $key => $label)
                <button @click="addQuestion('{{ $key }}')" type="button"
                    @class([
                        'text-xs font-medium bg-white border rounded-full px-3.5 py-2 transition shadow-sm',
                        'border-emerald-300 text-emerald-700 hover:bg-emerald-600 hover:text-white hover:border-emerald-600' => $key === 'checkbox',
                        'border-slate-200 hover:bg-violet-600 hover:text-white hover:border-violet-600' => $key !== 'checkbox',
                    ])>
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>
        </div>
    </div>

    {{-- Settings --}}
    <div x-show="tab === 'settings'" class="p-6 lg:p-10 max-w-2xl mx-auto">
        <form method="POST" action="{{ route('admin.forms.update', $form) }}" class="card p-6 lg:p-8 space-y-6">
            @csrf @method('PUT')
            <input type="hidden" name="title" :value="formTitle">
            <input type="hidden" name="description" :value="formDescription">

            <h3 class="font-bold text-fc text-lg">{{ __('app.builder.settings_title') }}</h3>

            <div>
                <label class="block text-sm font-semibold text-fc mb-2">{{ __('app.forms.form_type') }}</label>
                <div class="space-y-2">
                    @foreach(\App\Models\Form::formTypeLabels() as $type => $label)
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="form_type" value="{{ $type }}" x-model="formType" class="text-violet-600">
                        <span class="text-sm text-fc">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div x-show="formType === 'psychology'" x-cloak class="space-y-4 p-4 rounded-xl bg-sky-50 border border-sky-200">
                <div>
                    <h4 class="font-semibold text-fc text-sm">{{ __('app.builder.psych_results_title') }}</h4>
                    <p class="text-xs text-fc-muted mt-1">{{ __('app.builder.psych_results_hint') }}</p>
                </div>
                <template x-for="(range, rIndex) in psychologyResults" :key="rIndex">
                    <div class="p-4 bg-white rounded-lg border border-sky-100 space-y-3">
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <label class="text-xs text-fc-muted">{{ __('app.builder.psych_min') }}</label>
                                <input type="number" x-model.number="range.min" :name="'psychology_results['+rIndex+'][min]'" min="0" class="input text-sm py-1.5">
                            </div>
                            <div class="flex-1">
                                <label class="text-xs text-fc-muted">{{ __('app.builder.psych_max') }}</label>
                                <input type="number" x-model.number="range.max" :name="'psychology_results['+rIndex+'][max]'" min="0" class="input text-sm py-1.5">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs text-fc-muted">{{ __('app.builder.psych_result_title') }}</label>
                            <input type="text" x-model="range.title" :name="'psychology_results['+rIndex+'][title]'" class="input text-sm py-1.5">
                        </div>
                        <div>
                            <label class="text-xs text-fc-muted">{{ __('app.builder.psych_result_desc') }}</label>
                            <textarea x-model="range.description" :name="'psychology_results['+rIndex+'][description]'" rows="2" class="input text-sm resize-none"></textarea>
                        </div>
                        <button type="button" @click="psychologyResults.splice(rIndex, 1)" class="text-xs text-red-500 hover:text-red-600">{{ __('app.builder.psych_remove_range') }}</button>
                    </div>
                </template>
                <button type="button" @click="addPsychologyRange()" class="text-sm text-sky-600 font-medium hover:text-sky-700">+ {{ __('app.builder.psych_add_range') }}</button>
            </div>

            <div>
                <label class="block text-sm font-semibold text-fc mb-2">{{ __('app.builder.theme_color') }}</label>
                <div class="flex items-center gap-3">
                    <input type="color" name="theme_color" x-model="themeColor" class="w-12 h-12 rounded-xl cursor-pointer border-0">
                    <input type="text" x-model="themeColor" class="input w-32 text-sm font-mono">
                </div>
            </div>

            <div class="space-y-3 divide-y divide-slate-100">
                @foreach([
                    ['is_active', 'builder.form_active', null],
                    ['accept_responses', 'builder.accept_responses', null],
                    ['collect_email', 'builder.collect_email', null],
                    ['limit_one_response', 'builder.limit_one', null],
                    ['shuffle_questions', 'builder.shuffle', null],
                    ['show_progress_bar', 'builder.progress_bar', null],
                ] as [$name, $langKey, $xModel])
                <label class="flex items-center gap-3 py-3 cursor-pointer">
                    <input type="hidden" name="{{ $name }}" value="0">
                    <input type="checkbox" name="{{ $name }}" value="1"
                        @if($xModel) x-model="{{ $xModel }}" @else {{ $form->{$name} ? 'checked' : '' }} @endif
                        class="rounded border-slate-300 text-violet-600 w-4 h-4">
                    <span class="text-sm text-fc">{{ __("app.$langKey") }}</span>
                </label>
                @endforeach
            </div>

            <div>
                <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.builder.thank_you_message') }}</label>
                <textarea name="confirmation_message" rows="2" placeholder="{{ __('app.builder.thank_you_placeholder') }}" class="input resize-none">{{ $form->confirmation_message }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary w-full py-3">{{ __('app.builder.save_settings') }}</button>
        </form>
    </div>

    {{-- Share --}}
    @php
        $editShareConfig = [
            'fullUrl' => $form->publicUrl(),
            'shortUrl' => $form->shortUrl(),
            'shortLinkRoute' => route('admin.forms.short-link', $form),
            'csrf' => csrf_token(),
            'i18n' => [
                'copied' => __('app.common.copied'),
                'shortError' => __('app.builder.short_link_error'),
                'qrError' => __('app.builder.qr_error'),
            ],
        ];
    @endphp
    <div x-show="tab === 'share'" class="p-6 lg:p-10 max-w-2xl mx-auto">
        <div class="card p-6 lg:p-8" x-data='createShareTools(@json($editShareConfig))'>
            <h3 class="font-bold text-fc text-lg mb-1">{{ __('app.builder.share_link') }}</h3>
            <p class="text-sm text-fc-muted mb-6">{{ __('app.builder.share_tab_hint') }}</p>

            <div class="bg-violet-500/10 rounded-xl p-4 border border-violet-500/20 space-y-4">
                <div>
                    <p class="text-xs font-semibold text-fc-muted uppercase tracking-wide mb-2">{{ __('app.builder.full_link') }}</p>
                    <div class="flex gap-2">
                        <input type="text" readonly :value="fullUrl" class="input flex-1 text-sm">
                        <button type="button" @click="copyText(fullUrl)" class="btn btn-primary shrink-0">{{ __('app.common.copy') }}</button>
                    </div>
                </div>

                <template x-if="shortUrl">
                    <div>
                        <p class="text-xs font-semibold text-fc-muted uppercase tracking-wide mb-2">{{ __('app.builder.short_link') }}</p>
                        <div class="flex gap-2">
                            <input type="text" readonly :value="shortUrl" class="input flex-1 text-sm font-mono">
                            <button type="button" @click="copyText(shortUrl)" class="btn btn-secondary shrink-0">{{ __('app.common.copy') }}</button>
                        </div>
                    </div>
                </template>

                <div class="flex flex-wrap gap-2 pt-1">
                    <button type="button" x-show="!shortUrl" @click="createShortLink()" :disabled="creatingShort"
                        class="btn btn-secondary text-sm py-2.5 px-4 inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        {{ __('app.builder.create_short_link') }}
                    </button>
                    <button type="button" @click="toggleQr()" :disabled="generatingQr"
                        class="btn btn-secondary text-sm py-2.5 px-4 inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        <span x-text="showQr ? @json(__('app.builder.hide_qr')) : @json(__('app.builder.show_qr'))"></span>
                    </button>
                </div>

                <div x-show="showQr" x-cloak class="pt-2">
                    <div class="p-4 bg-white rounded-xl border border-violet-200 inline-block">
                        <canvas x-ref="qrCanvas" class="rounded-lg"></canvas>
                        <p class="text-xs text-fc-muted mt-2 max-w-[13rem] break-all" x-text="qrTargetUrl"></p>
                        <button type="button" @click="downloadQr()" class="btn btn-secondary text-xs py-1.5 mt-2">{{ __('app.builder.download_qr') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function formBuilder() {
    return {
        tab: 'questions',
        formTitle: @json($form->title),
        formDescription: @json($form->description ?? ''),
        formTranslations: @json($formTranslations),
        builderLocale: 'uz',
        themeColor: @json($form->theme_color),
        formType: @json($form->resolvedFormType()),
        isQuiz: @json($form->isQuiz()),
        isPsychology: @json($form->isPsychologyTest()),
        psychologyResults: @json($form->settings['psychology_results'] ?? []),
        sections: @json($builderSections),
        questions: @json($builderQuestions),
        saving: false,
        saved: false,
        i18n: @json($builderI18n),
        uidCounter: 0,

        init() {
            if (this.psychologyResults.length === 0 && this.formType === 'psychology') {
                this.addPsychologyRange();
            }
            this.questions.forEach(q => {
                q.translations = q.translations || { ru: { title: '', description: '' }, en: { title: '', description: '' } };
                (q.options || []).forEach(o => {
                    o.translations = o.translations || { ru: '', en: '' };
                });
            });
            this.$watch('formType', (value) => {
                this.isQuiz = value === 'quiz';
                this.isPsychology = value === 'psychology';
            });
            this.$nextTick(() => this.initSortable());
        },

        addPsychologyRange() {
            this.psychologyResults.push({ min: 0, max: 10, title: '', description: '' });
        },

        initSortable() {
            const el = document.getElementById('questions-list');
            if (!el || !window.Sortable) return;
            Sortable.create(el, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: () => {
                    const uids = [...el.querySelectorAll('.question-card')].map(c => c.dataset.uid);
                    this.questions = uids.map((uid, i) => {
                        const q = this.questions.find(x => x.uid === uid);
                        q.order = i;
                        return q;
                    });
                }
            });
        },

        newUid() { return 'new_' + (++this.uidCounter) + '_' + Date.now(); },

        hasOptions(type) {
            return ['multiple_choice', 'checkbox', 'dropdown'].includes(type);
        },

        typePreview(type) {
            return this.i18n.preview[type] || '';
        },

        onTypeChange(question) {
            if (this.hasOptions(question.type) && (!question.options || question.options.length === 0)) {
                question.options = [
                    { uid: this.newUid(), text: 'Variant 1', translations: { ru: '', en: '' }, is_correct: false, score_value: 0, order: 0 },
                    { uid: this.newUid(), text: 'Variant 2', translations: { ru: '', en: '' }, is_correct: false, score_value: 0, order: 1 },
                ];
            }
            if (question.type === 'linear_scale') {
                question.settings = question.settings || { min: 1, max: 5, min_label: 'Yomon', max_label: 'A\'lo' };
            }
        },

        addQuestion(type) {
            const q = {
                uid: this.newUid(),
                id: null,
                section_id: null,
                type,
                title: '',
                description: '',
                translations: { ru: { title: '', description: '' }, en: { title: '', description: '' } },
                is_required: false,
                order: this.questions.length,
                points: 1,
                settings: type === 'linear_scale' ? { min: 1, max: 5, min_label: 'Yomon', max_label: 'A\'lo' } : {},
                options: [],
            };
            this.onTypeChange(q);
            this.questions.push(q);
        },

        addConsentCheckbox() {
            const q = {
                uid: this.newUid(),
                id: null,
                section_id: null,
                type: 'checkbox',
                title: '',
                description: '',
                translations: { ru: { title: '', description: '' }, en: { title: '', description: '' } },
                is_required: true,
                order: this.questions.length,
                points: 1,
                settings: {},
                options: [{
                    uid: this.newUid(),
                    text: 'Roziman',
                    translations: { ru: 'Согласен(согласна).', en: 'I agree' },
                    is_correct: false,
                    score_value: 0,
                    order: 0,
                }],
            };
            this.questions.push(q);
        },

        addSection() {
            const title = prompt(this.i18n.section_prompt);
            if (!title) return;
            this.sections.push({ uid: this.newUid(), id: null, title, description: '', order: this.sections.length });
        },

        addOption(question) {
            question.options.push({ uid: this.newUid(), text: '', translations: { ru: '', en: '' }, is_correct: false, score_value: 0, order: question.options.length });
        },

        removeOption(question, index) {
            if (question.options.length > 1) question.options.splice(index, 1);
        },

        removeQuestion(index) {
            if (confirm(this.i18n.delete_question)) {
                this.questions.splice(index, 1);
            }
        },

        async saveStructure() {
            const emptyTitles = this.questions.filter(q => !String(q.title || '').trim());
            if (emptyTitles.length) {
                alert(this.i18n.empty_question_titles);
                return;
            }

            this.saving = true;
            this.saved = false;
            const payload = {
                form: {
                    title: String(this.formTitle || '').trim(),
                    description: this.formDescription,
                    translations: this.formTranslations,
                },
                sections: this.sections.map((s, i) => ({ ...s, order: i })),
                questions: this.questions.map((q, i) => ({
                    id: q.id,
                    section_id: q.section_id,
                    type: q.type,
                    title: String(q.title).trim(),
                    description: q.description,
                    is_required: q.is_required,
                    order: i,
                    points: q.points || 0,
                    settings: {
                        ...(q.settings || {}),
                        translations: q.translations || { ru: { title: '', description: '' }, en: { title: '', description: '' } },
                    },
                    options: (q.options || []).map((o, j) => ({
                        id: o.id,
                        text: o.text,
                        translations: o.translations || { ru: '', en: '' },
                        is_correct: o.is_correct,
                        score_value: o.score_value ?? 0,
                        order: j,
                    })),
                })),
            };

            try {
                const res = await fetch('{{ route('admin.forms.structure', $form) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (data.success) {
                    this.sections = data.sections || this.sections;
                    this.questions = data.questions || this.questions;
                    this.saved = true;
                    setTimeout(() => this.saved = false, 3000);
                    this.$nextTick(() => this.initSortable());
                }
            } catch (e) {
                alert(this.i18n.save_error);
            }
            this.saving = false;
        },

        saveSettings() {
            // Title/description auto-saved via structure save on blur is handled separately
        },
    };
}
</script>
@endsection
