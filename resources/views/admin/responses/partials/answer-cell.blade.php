@if($answer && $answer->file_path)
    @php $viewUrl = $answer->viewUrl($form, $response); @endphp
    @if($answer->isImage() && $viewUrl)
    <a href="{{ $viewUrl }}" target="_blank" rel="noopener" class="response-file-thumb-link" title="{{ basename($answer->file_path) }}">
        <span class="response-file-thumb-wrap">
            <img src="{{ $viewUrl }}" alt="" class="response-file-thumb" loading="lazy"
                onerror="this.classList.add('is-hidden'); this.nextElementSibling.classList.add('is-visible');">
            <span class="response-file-thumb-fallback" aria-hidden="true">
                <svg class="response-user-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </span>
        </span>
    </a>
    @elseif($viewUrl)
    <a href="{{ $viewUrl }}" target="_blank" rel="noopener" class="response-view-file-btn">
        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        {{ __('app.responses.open_file') }}
    </a>
    @else
    <span class="response-file-thumb-wrap response-file-thumb-wrap--static" title="{{ basename($answer->file_path) }}">
        <span class="response-file-thumb-fallback is-visible" aria-hidden="true">
            <svg class="response-user-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </span>
    </span>
    @endif
@elseif($answer)
    @php $text = $answer->displayValue(); @endphp
    <span title="{{ $text }}">{{ $text ? Str::limit($text, $compact ? 40 : 200) : '—' }}</span>
@else
    <span class="text-fc-muted">—</span>
@endif
