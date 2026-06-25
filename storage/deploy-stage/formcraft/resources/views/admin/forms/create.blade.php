@extends('admin.layout')

@section('title', __('app.forms.create'))

@section('content')
<div class="p-6 lg:p-10 max-w-2xl mx-auto" x-data="{ formType: @json(old('form_type', 'survey')) }">
    <a href="{{ route('admin.forms.index') }}" class="inline-flex items-center gap-1.5 text-sm text-fc-muted hover:text-violet-600 mb-6 font-medium">← {{ __('app.common.back') }}</a>
    <h1 class="text-2xl font-bold text-fc mb-2">{{ __('app.forms.create') }}</h1>
    <p class="text-fc-muted mb-8">{{ __('app.forms.create_subtitle') }}</p>
    <div class="card p-8">
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('admin.forms.store') }}" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.forms.form_name') }} *</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="input input-lg" placeholder="{{ __('app.forms.form_name_placeholder') }}">
                @error('title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-fc mb-1.5">{{ __('app.forms.description') }}</label>
                <textarea name="description" rows="3" class="input resize-none" placeholder="{{ __('app.forms.description_placeholder') }}">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-fc mb-3">{{ __('app.forms.form_type') }} *</label>
                <div class="space-y-3">
                    @foreach(\App\Models\Form::formTypeLabels() as $type => $label)
                    <label
                        class="flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition"
                        :class="formType === @json($type)
                            ? 'border-violet-500 bg-violet-50/60 shadow-sm ring-1 ring-violet-200'
                            : 'border-dashed border-[var(--fc-border)] hover:border-violet-300'">
                        <input type="radio" name="form_type" value="{{ $type }}" x-model="formType" class="mt-1 text-violet-600">
                        <div>
                            <div class="font-semibold text-fc">{{ $label }}</div>
                            <div class="text-sm text-fc-muted mt-1">{{ __("app.forms.types.{$type}_desc") }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('form_type')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="btn btn-primary w-full py-3">{{ __('app.forms.create_and_edit') }} →</button>
        </form>
    </div>
</div>
@endsection
