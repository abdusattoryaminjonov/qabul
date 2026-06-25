<?php

namespace App\Services;

use App\Models\Form;
use App\Models\FormQuestion;
use App\Support\FormTranslations;
use Illuminate\Http\Request;

class PsychologyScoringService
{
    public function scoreQuestion(FormQuestion $question, mixed $value): int
    {
        if ($question->type === 'linear_scale') {
            return is_numeric($value) ? (int) $value : 0;
        }

        if (! $question->hasOptions()) {
            return 0;
        }

        $selected = collect(is_array($value) ? $value : [$value])->filter()->values();
        $score = 0;

        foreach ($question->options as $option) {
            if ($selected->contains($option->text)) {
                $score += (int) ($option->score_value ?? 0);
            }
        }

        return $score;
    }

    /**
     * @return array{total: int, breakdown: array<int, int>}
     */
    public function calculateFromRequest(Form $form, Request $request): array
    {
        $total = 0;
        $breakdown = [];

        foreach ($form->questions as $question) {
            $value = $request->input('answers.'.$question->id);
            $points = $this->scoreQuestion($question, $value);
            $breakdown[$question->id] = $points;
            $total += $points;
        }

        return ['total' => $total, 'breakdown' => $breakdown];
    }

    public function findResult(Form $form, int $score, ?string $locale = null): ?array
    {
        $ranges = $form->settings['psychology_results'] ?? [];
        $locale = FormTranslations::locale($locale);

        foreach ($ranges as $range) {
            $min = (int) ($range['min'] ?? 0);
            $max = (int) ($range['max'] ?? PHP_INT_MAX);

            if ($score >= $min && $score <= $max) {
                $title = $range['title'] ?? '';
                $description = $range['description'] ?? '';

                if ($locale !== 'uz' && ! empty($range['translations'][$locale])) {
                    $title = $range['translations'][$locale]['title'] ?? $title;
                    $description = $range['translations'][$locale]['description'] ?? $description;
                }

                return [
                    'title' => $title,
                    'description' => $description,
                    'min' => $min,
                    'max' => $max,
                ];
            }
        }

        return null;
    }
}
