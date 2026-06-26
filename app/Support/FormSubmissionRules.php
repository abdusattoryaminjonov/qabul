<?php

namespace App\Support;

use App\Models\Form;
use App\Models\FormQuestion;

class FormSubmissionRules
{
    public const TEXT_MAX = 5000;

    public const FILE_MAX_KB = 10240;

    public const FILE_MIMES = 'jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt';

    public static function forForm(Form $form, bool $collectEmail): array
    {
        $rules = [];

        if ($collectEmail) {
            $rules['respondent_email'] = ['required', 'email', 'max:255'];
        }

        foreach ($form->questions as $question) {
            $rules = array_merge($rules, self::forQuestion($question));
        }

        return $rules;
    }

    public static function forQuestion(FormQuestion $question): array
    {
        $key = 'answers.'.$question->id;

        if ($question->type === 'file') {
            return [
                $key => $question->is_required
                    ? ['required', 'file', 'max:'.self::FILE_MAX_KB, 'mimes:'.self::FILE_MIMES]
                    : ['nullable', 'file', 'max:'.self::FILE_MAX_KB, 'mimes:'.self::FILE_MIMES],
            ];
        }

        if ($question->type === 'checkbox') {
            return [
                $key => $question->is_required
                    ? ['required', 'array', 'min:1', 'max:50']
                    : ['nullable', 'array', 'max:50'],
                $key.'.*' => ['string', 'max:500'],
            ];
        }

        if ($question->is_required) {
            return [$key => ['required', 'string', 'max:'.self::TEXT_MAX]];
        }

        return [$key => ['nullable', 'string', 'max:'.self::TEXT_MAX]];
    }

    public static function attributeNames(Form $form): array
    {
        $names = [
            'respondent_email' => __('app.public.email_required'),
        ];

        foreach ($form->questions as $question) {
            $names['answers.'.$question->id] = $question->localizedTitle();
        }

        return $names;
    }

    public static function fieldHasError($errors, string $fieldKey): bool
    {
        if ($errors->has($fieldKey)) {
            return true;
        }

        return collect($errors->keys())->contains(
            fn (string $key) => str_starts_with($key, $fieldKey.'.')
        );
    }
}
