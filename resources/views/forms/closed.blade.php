@extends('forms.layout')

@section('title', __('app.public.form_closed'))

@push('head')
<style>:root { --form-theme: #2dd4bf; }</style>
@endpush

@section('body')
<div class="public-shell-wrap public-shell-wrap-narrow">
    <div class="public-result-card text-center">
        <div class="public-result-icon public-result-icon-muted">
            <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <h1 class="text-xl font-bold text-fc mb-2">{{ __('app.public.form_closed') }}</h1>
        <p class="text-fc-muted leading-relaxed">{{ $message }}</p>
    </div>
</div>
@endsection
