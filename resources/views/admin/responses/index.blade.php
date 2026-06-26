@extends('admin.layout')

@section('title', __('app.responses.title'))

@section('content')
<div class="p-6 lg:p-10">
    <a href="{{ route('admin.responses.forms') }}" class="inline-flex items-center gap-1.5 text-sm text-fc-muted hover:text-violet-600 font-medium mb-4">← {{ $form->title }}</a>
    <div class="flex flex-wrap justify-between items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-fc">{{ __('app.responses.title') }}</h1>
            <p class="text-fc-muted text-sm mt-1">{{ __('app.responses.count', ['count' => $responses->total()]) }}</p>
        </div>
        @can('viewResponses', $form)
        <a href="{{ route('admin.responses.analytics', $form) }}" class="btn btn-secondary" title="{{ __('app.common.statistics') }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            {{ __('app.common.statistics') }}
        </a>
        @endcan
    </div>

    @if($responses->isEmpty())
    <div class="card p-14 text-center">
        <p class="text-fc-muted mb-3">{{ __('app.responses.empty') }}</p>
        <p class="text-sm text-fc-muted">{{ __('app.responses.share_hint') }}</p>
        <a href="{{ $form->publicUrl() }}" class="text-violet-600 font-medium text-sm mt-1 inline-block">{{ $form->publicUrl() }}</a>
    </div>
    @else
    <div class="card mb-6">
        <div class="px-5 py-4 border-b border-[var(--fc-border)] bg-[var(--fc-hover)]">
            <h2 class="font-semibold text-fc">{{ __('app.responses.table_preview') }}</h2>
            <p class="text-xs text-fc-muted mt-1">{{ __('app.responses.table_hint') }}</p>
        </div>
        <div class="responses-table-wrap">
            <table class="responses-table text-sm">
                <thead><tr>
                    <th class="col-num">#</th>
                    @foreach($previewQuestions as $question)
                    <th class="col-question" title="{{ $question->title }}">{{ Str::limit($question->title, 28) }}</th>
                    @endforeach
                    @if($form->isQuiz() || $form->isPsychologyTest())<th class="col-score">{{ __('app.common.points') }}</th>@endif
                    <th class="col-date">{{ __('app.common.date') }}</th>
                    <th class="col-actions text-right">{{ __('app.common.actions') }}</th>
                </tr></thead>
                <tbody>
                    @foreach($responses as $i => $response)
                    <tr>
                        <td class="col-num font-medium">{{ ($responses->currentPage() - 1) * $responses->perPage() + $i + 1 }}</td>
                        @foreach($previewQuestions as $question)
                        @php $answer = $response->answers->firstWhere('question_id', $question->id); @endphp
                        <td class="col-answer">
                            @include('admin.responses.partials.answer-cell', [
                                'form' => $form,
                                'response' => $response,
                                'answer' => $answer,
                                'compact' => true,
                            ])
                        </td>
                        @endforeach
                        @if($form->isQuiz())
                        <td class="col-score"><span class="badge badge-brand">{{ $response->score }}/{{ $response->max_score }}</span></td>
                        @elseif($form->isPsychologyTest())
                        <td class="col-score"><span class="badge badge-brand">{{ $response->score }}</span></td>
                        @endif
                        <td class="col-date text-fc-muted whitespace-nowrap">{{ $response->submitted_at->format('d.m.Y H:i') }}</td>
                        <td class="col-actions text-right">
                            <a href="{{ route('admin.responses.show', [$form, $response]) }}" class="action-icon" data-tooltip="{{ __('app.responses.view_all') }}" aria-label="{{ __('app.responses.view_all') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @can('deleteResponses', $form)
                            <form action="{{ route('admin.responses.destroy', [$form, $response]) }}" method="POST" class="inline" onsubmit="return confirm(@json(__('app.common.confirm_delete_short')))">@csrf @method('DELETE')
                                <button type="submit" class="action-icon action-icon-danger" data-tooltip="{{ __('app.common.delete') }}" aria-label="{{ __('app.common.delete') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $responses->links() }}</div>

    @can('exportResponses', $form)
    <div class="mt-8 card p-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="font-semibold text-fc">{{ __('app.responses.export_title') }}</h3>
            <p class="text-sm text-fc-muted mt-1">{{ __('app.responses.export_hint') }}</p>
        </div>
        <a href="{{ route('admin.responses.export', $form) }}" class="btn btn-primary shrink-0" style="background:linear-gradient(135deg,#059669,#047857)" title="{{ __('app.common.export_excel') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            {{ __('app.common.export_excel') }}
        </a>
    </div>
    @endcan
    @endif
</div>
@endsection
