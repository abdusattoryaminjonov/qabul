<?php

namespace App\Support;

class FormTranslations
{
    public const LOCALES = ['uz', 'ru', 'en'];

    public static function locale(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return in_array($locale, self::LOCALES, true) ? $locale : 'uz';
    }

    /**
     * @param  array<string, array<string, string>>|null  $translations
     */
    public static function resolve(?array $translations, string $field, string $fallback, ?string $locale = null): string
    {
        $locale = self::locale($locale);

        if (! empty($translations[$locale][$field])) {
            return $translations[$locale][$field];
        }

        if ($locale !== 'uz' && ! empty($translations['uz'][$field])) {
            return $translations['uz'][$field];
        }

        return $fallback;
    }

    /**
     * @param  array<string, string>|null  $translations
     */
    public static function resolveText(?array $translations, string $fallback, ?string $locale = null): string
    {
        $locale = self::locale($locale);

        if (! empty($translations[$locale])) {
            return $translations[$locale];
        }

        if ($locale !== 'uz' && ! empty($translations['uz'])) {
            return $translations['uz'];
        }

        return $fallback;
    }

    public static function labels(): array
    {
        return [
            'uz' => "O'zbek",
            'ru' => 'Русский',
            'en' => 'English',
        ];
    }
}
