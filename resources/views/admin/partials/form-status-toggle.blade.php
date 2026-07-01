@php
    $isActive = $form->isPublished();
@endphp

@can('update', $form)
<form method="POST" action="{{ route('admin.forms.toggle-status', $form) }}" class="form-status-toggle">
    @csrf
    @method('PATCH')
    <input type="hidden" name="active" value="0">
    <label class="toggle-switch" title="{{ $isActive ? __('app.forms.deactivate') : __('app.forms.activate') }}">
        <input type="checkbox" name="active" value="1" class="toggle-switch-input" @checked($isActive) onchange="this.form.requestSubmit()">
        <span class="toggle-switch-track" aria-hidden="true">
            <span class="toggle-switch-thumb"></span>
        </span>
        <span class="toggle-switch-text">{{ $isActive ? __('app.common.active') : __('app.common.closed') }}</span>
    </label>
</form>
@else
<div class="form-status-readonly">
    @if($isActive)
    <span class="badge badge-success">{{ __('app.common.active') }}</span>
    @else
    <span class="badge badge-muted">{{ __('app.common.closed') }}</span>
    @endif
</div>
@endcan
