<?php

namespace App\Services;

use App\Models\Form;
use App\Models\FormResponse;

class ResponseExportService
{
    public static function headers(Form $form): array
    {
        $headers = ['#', __('app.common.date')];

        if ($form->collect_email) {
            $headers[] = __('app.common.email');
        }

        foreach ($form->questions as $question) {
            $headers[] = $question->title;
        }

        if ($form->isQuiz()) {
            $headers[] = __('app.common.points');
        } elseif ($form->isPsychologyTest()) {
            $headers[] = __('app.public.psych_total_score');
        }

        return $headers;
    }

    public static function row(Form $form, FormResponse $response, int $index): array
    {
        $row = [
            $index,
            $response->submitted_at->format('d.m.Y H:i'),
        ];

        if ($form->collect_email) {
            $row[] = $response->respondent_email ?? '';
        }

        foreach ($form->questions as $question) {
            $answer = $response->answers->firstWhere('question_id', $question->id);
            $row[] = $answer ? $answer->exportValue($form, $response) : '';
        }

        if ($form->isQuiz()) {
            $row[] = $response->score !== null ? $response->score.'/'.$response->max_score : '';
        } elseif ($form->isPsychologyTest()) {
            $row[] = $response->score ?? '';
        }

        return $row;
    }
}
