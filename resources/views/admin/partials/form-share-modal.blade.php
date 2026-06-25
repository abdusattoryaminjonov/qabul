<div x-show="show" x-cloak class="share-modal-backdrop" @click.self="close()" @keydown.escape.window="show && close()">
    <div class="share-modal" role="dialog" aria-modal="true" aria-labelledby="share-modal-title">
        <div class="share-modal-header">
            <div>
                <h3 id="share-modal-title" class="share-modal-title">{{ __('app.share.modal_title') }}</h3>
                <p class="share-modal-subtitle" x-text="formTitle"></p>
            </div>
            <button type="button" class="share-modal-close" @click="close()" aria-label="{{ __('app.common.close') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <p class="share-modal-hint">{{ __('app.share.modal_hint') }}</p>

        <div class="share-modal-options">
            <button type="button" class="share-option" @click="chooseFull()" :disabled="busy">
                <span class="share-option-icon share-option-icon-violet">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </span>
                <span class="share-option-body">
                    <span class="share-option-title">{{ __('app.share.option_full') }}</span>
                    <span class="share-option-desc">{{ __('app.share.option_full_desc') }}</span>
                </span>
            </button>

            <button type="button" class="share-option" @click="chooseShort()" :disabled="busy">
                <span class="share-option-icon share-option-icon-emerald">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
                <span class="share-option-body">
                    <span class="share-option-title">{{ __('app.share.option_short') }}</span>
                    <span class="share-option-desc">{{ __('app.share.option_short_desc') }}</span>
                </span>
            </button>

            <button type="button" class="share-option" @click="chooseQrShort()" :disabled="busy">
                <span class="share-option-icon share-option-icon-sky">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </span>
                <span class="share-option-body">
                    <span class="share-option-title">{{ __('app.share.option_qr') }}</span>
                    <span class="share-option-desc">{{ __('app.share.option_qr_desc') }}</span>
                </span>
            </button>
        </div>

        <div class="share-modal-footer">
            <button type="button" class="btn btn-secondary w-full" @click="close()">{{ __('app.common.cancel') }}</button>
        </div>
    </div>
</div>
