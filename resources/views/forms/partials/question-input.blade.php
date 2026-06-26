@php
    $min = $question->settings['min'] ?? 1;
    $max = $question->settings['max'] ?? 5;
    $inputClass = 'input public-input'.(($fieldError ?? false) ? ' public-input--error' : '');
@endphp
@switch($question->type)
    @case('short_text')
        <input type="text" name="answers[{{ $question->id }}]" value="{{ old('answers.'.$question->id) }}" class="{{ $inputClass }}" placeholder="{{ __('app.public.short_text_placeholder') }}">
        @break
    @case('paragraph')
        <textarea name="answers[{{ $question->id }}]" rows="4" class="{{ $inputClass }} resize-y min-h-[6rem]" placeholder="{{ __('app.public.long_text_placeholder') }}">{{ old('answers.'.$question->id) }}</textarea>
        @break
    @case('multiple_choice')
        <div class="public-choices">@foreach($question->options as $option)
            <label class="choice-option public-choice">
                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->text }}" {{ old('answers.'.$question->id) === $option->text ? 'checked' : '' }} class="public-choice-input">
                <span class="public-choice-marker"></span>
                <span>{{ $option->localizedText() }}</span>
            </label>
        @endforeach</div>
        @break
    @case('checkbox')
        <div class="public-choices">@foreach($question->options as $option)
            <label class="choice-option public-choice">
                <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option->text }}" {{ in_array($option->text, old('answers.'.$question->id, [])) ? 'checked' : '' }} class="public-choice-input">
                <span class="public-choice-marker public-choice-marker-check"></span>
                <span>{{ $option->localizedText() }}</span>
            </label>
        @endforeach</div>
        @break
    @case('dropdown')
        <select name="answers[{{ $question->id }}]" class="{{ $inputClass }}">
            <option value="">{{ __('app.common.select') }}</option>
            @foreach($question->options as $option)
            <option value="{{ $option->text }}" {{ old('answers.'.$question->id) === $option->text ? 'selected' : '' }}>{{ $option->localizedText() }}</option>
            @endforeach
        </select>
        @break
    @case('linear_scale')
        <div class="mt-1">
            <div class="flex justify-between text-xs font-medium text-fc-muted mb-3 px-0.5">
                <span>{{ $question->localizedScaleLabel('min_label') ?: $min }}</span>
                <span>{{ $question->localizedScaleLabel('max_label') ?: $max }}</span>
            </div>
            <div class="public-scale">@for($i = $min; $i <= $max; $i++)
                <label class="public-scale-item">
                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $i }}" {{ old('answers.'.$question->id) == $i ? 'checked' : '' }} class="sr-only public-scale-radio">
                    <span class="public-scale-label">{{ $i }}</span>
                </label>
            @endfor</div>
        </div>
        @break
    @case('date')
        <input type="date" name="answers[{{ $question->id }}]" value="{{ old('answers.'.$question->id) }}" class="{{ $inputClass }} w-full sm:w-auto">
        @break
    @case('time')
        <input type="time" name="answers[{{ $question->id }}]" value="{{ old('answers.'.$question->id) }}" class="{{ $inputClass }} w-full sm:w-auto">
        @break
    @case('file')
        <label class="public-file-upload @if($fieldError ?? false) public-file-upload--error @endif">
            <input type="file" name="answers[{{ $question->id }}]" class="sr-only">
            <svg class="w-8 h-8 text-fc-muted mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            <span class="text-sm font-medium text-fc">{{ __('app.public.file_upload') }}</span>
            <span class="text-xs text-fc-muted mt-1">{{ __('app.public.file_hint') }}</span>
        </label>
        @break
@endswitch
