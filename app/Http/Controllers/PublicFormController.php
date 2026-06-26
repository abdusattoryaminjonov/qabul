<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormResponse;
use App\Models\ResponseAnswer;
use App\Services\PsychologyScoringService;
use App\Support\FormSubmissionRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;

class PublicFormController extends Controller
{
    public function __construct(
        private PsychologyScoringService $psychologyScoring
    ) {}

    public function shortRedirect(string $code)
    {
        $form = Form::where('short_code', $code)
            ->where('is_active', true)
            ->firstOrFail();

        return redirect()->route('forms.show', $form->slug);
    }

    public function show(string $slug)
    {
        $form = Form::where('slug', $slug)
            ->where('is_active', true)
            ->with(['questions.options', 'sections'])
            ->firstOrFail();

        if ($form->limit_one_response && Cookie::get('form_submitted_'.$form->id)) {
            return view('forms.closed', [
                'form' => $form,
                'message' => __('app.public.already_submitted'),
            ]);
        }

        if (! $form->accept_responses) {
            return view('forms.closed', [
                'form' => $form,
                'message' => __('app.public.not_accepting'),
            ]);
        }

        $questions = $form->questions;
        if ($form->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        return view('forms.show', compact('form', 'questions'));
    }

    public function submit(Request $request, string $slug)
    {
        $form = Form::where('slug', $slug)
            ->where('is_active', true)
            ->with(['questions.options'])
            ->firstOrFail();

        if (! $form->accept_responses) {
            return back()->with('error', __('app.public.not_accepting'));
        }

        if ($form->limit_one_response && Cookie::get('form_submitted_'.$form->id)) {
            return back()->with('error', __('app.public.already_submitted'));
        }

        $rules = FormSubmissionRules::forForm($form, $form->collect_email);

        $validated = $request->validate(
            $rules,
            [],
            FormSubmissionRules::attributeNames($form)
        );

        $score = 0;
        $maxScore = 0;
        $psychologyScore = null;
        $psychologyResult = null;

        if ($form->isQuiz()) {
            foreach ($form->questions as $question) {
                if ($question->hasOptions()) {
                    $maxScore += $question->points ?: 1;
                }
            }
        }

        if ($form->isPsychologyTest()) {
            $psychologyScore = $this->psychologyScoring->calculateFromRequest($form, $request);
            $psychologyResult = [
                'interpretation' => $this->psychologyScoring->findResult($form, $psychologyScore['total'], app()->getLocale()),
                'breakdown' => $psychologyScore['breakdown'],
            ];
        }

        $response = FormResponse::create([
            'form_id' => $form->id,
            'respondent_email' => $validated['respondent_email'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'score' => $form->isPsychologyTest() ? $psychologyScore['total'] : null,
            'max_score' => $form->isQuiz() ? $maxScore : null,
            'result_data' => $form->isPsychologyTest() ? $psychologyResult : null,
            'submitted_at' => now(),
        ]);

        foreach ($form->questions as $question) {
            $answerKey = 'answers.'.$question->id;
            $value = $request->input('answers.'.$question->id);

            $answerData = [
                'response_id' => $response->id,
                'question_id' => $question->id,
            ];

            if ($question->type === 'file' && $request->hasFile($answerKey)) {
                $path = $request->file($answerKey)->store('form-uploads/'.$form->id, 'local');
                $answerData['file_path'] = $path;
            } elseif ($question->type === 'checkbox' && is_array($value)) {
                $answerData['answer_json'] = $value;
            } elseif (is_array($value)) {
                $answerData['answer_json'] = $value;
            } else {
                $answerData['answer_text'] = $value;
            }

            ResponseAnswer::create($answerData);

            if ($form->isQuiz() && $question->hasOptions()) {
                $points = $question->points ?: 1;
                $correctOptions = $question->options->where('is_correct', true)->pluck('text')->sort()->values();
                $given = collect(is_array($value) ? $value : [$value])->filter()->sort()->values();

                if ($question->type === 'checkbox') {
                    if ($correctOptions->toJson() === $given->toJson()) {
                        $score += $points;
                    }
                } else {
                    $correct = $correctOptions->first();
                    if ($correct && $value === $correct) {
                        $score += $points;
                    }
                }
            }
        }

        if ($form->isQuiz()) {
            $response->update(['score' => $score]);
        }

        $redirect = redirect()->route('forms.thankyou', $form->slug);

        if ($form->isQuiz()) {
            $redirect->with('score', $score)->with('max_score', $maxScore);
        }

        if ($form->isPsychologyTest()) {
            $redirect->with('psychology_response', [
                'form_id' => $form->id,
                'response_id' => $response->id,
                'expires_at' => now()->addMinutes(30)->timestamp,
            ]);
        }

        if ($form->limit_one_response) {
            $redirect->cookie($this->submissionCookie($request, $form->id));
        }

        return $redirect;
    }

    public function thankyou(string $slug)
    {
        $form = Form::where('slug', $slug)->firstOrFail();

        $response = null;
        $answerRows = [];

        $psychologyAccess = session('psychology_response');

        if ($form->isPsychologyTest()
            && is_array($psychologyAccess)
            && ($psychologyAccess['form_id'] ?? null) === $form->id
            && ($psychologyAccess['expires_at'] ?? 0) >= now()->timestamp) {
            $response = FormResponse::with(['answers.question.options'])
                ->where('form_id', $form->id)
                ->find($psychologyAccess['response_id'] ?? null);

            if ($response) {
                $breakdown = collect($response->result_data['breakdown'] ?? [])
                    ->mapWithKeys(fn ($value, $key) => [(int) $key => $value])
                    ->all();

                foreach ($response->answers->sortBy(fn ($a) => $a->question?->order ?? 0) as $answer) {
                    $answerRows[] = [
                        'question' => $answer->question?->localizedTitle() ?? '',
                        'answer' => $this->localizedAnswerText($answer),
                        'points' => $breakdown[$answer->question_id] ?? null,
                    ];
                }
            }
        }

        return view('forms.thankyou', [
            'form' => $form,
            'score' => session('score'),
            'max_score' => session('max_score'),
            'response' => $response,
            'answerRows' => $answerRows,
        ]);
    }

    private function localizedAnswerText(ResponseAnswer $answer): string
    {
        if (! $answer->question) {
            return $answer->displayValue();
        }

        $answer->question->loadMissing('options');
        $value = $answer->answer_json ?? $answer->answer_text;

        if (is_array($value)) {
            return collect($value)
                ->map(fn ($item) => $this->localizedOptionText($answer->question, $item))
                ->filter()
                ->implode(', ');
        }

        return $this->localizedOptionText($answer->question, $value) ?: '—';
    }

    private function localizedOptionText(\App\Models\FormQuestion $question, mixed $storedText): string
    {
        if ($storedText === null || $storedText === '') {
            return '';
        }

        $option = $question->options->firstWhere('text', (string) $storedText);

        return $option?->localizedText() ?: (string) $storedText;
    }

    private function submissionCookie(Request $request, int $formId): SymfonyCookie
    {
        return Cookie::make(
            'form_submitted_'.$formId,
            '1',
            60 * 24 * 365,
            '/',
            null,
            $request->isSecure(),
            true,
            false,
            SymfonyCookie::SAMESITE_LAX
        );
    }
}
