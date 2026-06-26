@if($answer && $answer->file_path && $answer->viewUrl($form, $response))
    @if($answer->isImage())
    <div class="response-file-preview">
        <a href="{{ $answer->viewUrl($form, $response) }}" target="_blank" rel="noopener">
            <img src="{{ $answer->viewUrl($form, $response) }}" alt="{{ basename($answer->file_path) }}" class="response-file-preview-img">
        </a>
        <p class="text-xs text-fc-muted mt-2">{{ basename($answer->file_path) }}</p>
    </div>
    @elseif(str_ends_with(strtolower($answer->file_path), '.pdf'))
    <div class="response-file-preview">
        <iframe src="{{ $answer->viewUrl($form, $response) }}" class="response-file-preview-pdf" title="{{ basename($answer->file_path) }}"></iframe>
        <a href="{{ $answer->viewUrl($form, $response) }}" target="_blank" rel="noopener" class="text-violet-600 text-sm font-medium hover:underline mt-2 inline-block">
            {{ __('app.responses.open_file') }} — {{ basename($answer->file_path) }}
        </a>
    </div>
    @else
    <a href="{{ $answer->viewUrl($form, $response) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-violet-600 font-medium hover:underline">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        {{ __('app.responses.open_file') }} — {{ basename($answer->file_path) }}
    </a>
    @endif
@elseif($answer)
    {{ $answer->displayValue() ?: '—' }}
@else
    <span class="text-fc-muted italic">{{ __('app.responses.no_answer') }}</span>
@endif
