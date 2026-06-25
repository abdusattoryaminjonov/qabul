@props(['form'])

@php
    $shareDetail = [
        'title' => $form->title,
        'fullUrl' => $form->publicUrl(),
        'shortUrl' => $form->shortUrl(),
        'shortLinkRoute' => route('admin.forms.short-link', $form),
    ];
@endphp

<button type="button"
    class="action-icon"
    data-tooltip="{{ __('app.common.share') }}"
    aria-label="{{ __('app.common.share') }}"
    @click='openShare(@json($shareDetail))'>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684L12 3.632l3.367 1.684a3 3 0 105.367 2.684m0 0L12 12.684l-3.367-1.684z"/></svg>
</button>
