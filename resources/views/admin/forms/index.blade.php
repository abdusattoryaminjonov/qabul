@extends('admin.layout')

@section('title', __('app.forms.title'))

@section('content')
@php
    $shareI18n = [
        'copied' => __('app.common.copied'),
        'shortError' => __('app.builder.short_link_error'),
        'qrError' => __('app.builder.qr_error'),
    ];
@endphp
<div class="admin-page" x-data='formShareModal(@json($shareI18n))'>
    @if($forms->isEmpty())
    <div class="admin-panel-card">
        @include('admin.partials.page-header', [
            'title' => __('app.forms.title'),
            'count' => 0,
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
            'createUrl' => route('admin.forms.create'),
            'createLabel' => __('app.nav.new_form'),
        ])
        <div class="admin-empty-state">
            <p class="text-fc-muted mb-4">{{ __('app.forms.not_found') }}</p>
            <a href="{{ route('admin.forms.create') }}" class="btn btn-primary btn-create">{{ __('app.forms.create') }}</a>
        </div>
    </div>
    @else
    <div class="admin-panel-card">
        @include('admin.partials.page-header', [
            'title' => __('app.forms.title'),
            'count' => $forms->total(),
            'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
            'createUrl' => route('admin.forms.create'),
            'createLabel' => __('app.nav.new_form'),
        ])
        <div class="table-wrap admin-table-wrap">
            <table class="w-full">
                <thead>
                    <tr>
                        <th>{{ __('app.forms.form') }}</th>
                        @if(auth()->user()->canViewAllForms())<th>{{ __('app.forms.owner') }}</th>@endif
                        <th>{{ __('app.common.responses') }}</th>
                        <th>{{ __('app.common.status') }}</th>
                        <th>{{ __('app.common.created') }}</th>
                        <th class="text-right">{{ __('app.common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($forms as $form)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="admin-form-thumb" style="--form-color: {{ $form->theme_color }}">
                                    <div class="admin-form-thumb-dot"></div>
                                </div>
                                <div>
                                    <div class="font-semibold text-fc">{{ $form->title }}</div>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @if($form->isQuiz())<span class="badge badge-category">{{ __('app.common.test') }}</span>@endif
                                        @if($form->isPsychologyTest())<span class="badge badge-category">{{ __('app.common.psychology_test') }}</span>@endif
                                        @if($form->isRegistration())<span class="badge badge-category">{{ __('app.common.registration') }}</span>@endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        @if(auth()->user()->canViewAllForms())
                        <td class="text-fc-muted text-sm">{{ $form->user->name ?? '—' }}</td>
                        @endif
                        <td><span class="font-semibold">{{ $form->responses_count }}</span></td>
                        <td>
                            @include('admin.partials.form-status-toggle', ['form' => $form])
                        </td>
                        <td class="text-fc-muted whitespace-nowrap">{{ $form->created_at->format('d.m.Y') }}</td>
                        <td>
                            <div class="flex justify-end items-center gap-1">
                                @include('admin.partials.form-share-button', ['form' => $form])
                                <a href="{{ $form->publicUrl() }}" target="_blank" class="action-icon action-icon-table" data-tooltip="{{ __('app.common.view') }}" aria-label="{{ __('app.common.view') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                                @can('viewResponses', $form)
                                <a href="{{ route('admin.responses.index', $form) }}" class="action-icon action-icon-table" data-tooltip="{{ __('app.common.responses') }}" aria-label="{{ __('app.common.responses') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                </a>
                                @endcan
                                @can('update', $form)
                                <a href="{{ route('admin.forms.edit', $form) }}" class="action-icon action-icon-table" data-tooltip="{{ __('app.common.edit') }}" aria-label="{{ __('app.common.edit') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('admin.forms.duplicate', $form) }}" method="POST" class="inline">@csrf
                                    <button type="submit" class="action-icon action-icon-table" data-tooltip="{{ __('app.common.duplicate') }}" aria-label="{{ __('app.common.duplicate') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </button>
                                </form>
                                @endcan
                                @can('delete', $form)
                                <form action="{{ route('admin.forms.destroy', $form) }}" method="POST" class="inline" onsubmit="return confirm(@json(__('app.common.confirm_delete')))">@csrf @method('DELETE')
                                    <button type="submit" class="action-icon action-icon-table action-icon-danger" data-tooltip="{{ __('app.common.delete') }}" aria-label="{{ __('app.common.delete') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($forms->hasPages())
        <div class="admin-pagination">{{ $forms->links() }}</div>
        @endif
    </div>
    @endif
    @include('admin.partials.form-share-modal')
</div>
@endsection
