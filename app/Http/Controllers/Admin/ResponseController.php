<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormResponse;
use App\Models\ResponseAnswer;
use App\Services\ResponseExportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResponseController extends Controller
{
    public function formsList()
    {
        $forms = $this->viewableFormsForResponses()->paginate(12);

        return view('admin.responses.forms-picker', [
            'forms' => $forms,
            'mode' => 'responses',
        ]);
    }

    public function statisticsList()
    {
        $forms = $this->viewableFormsForResponses()->paginate(12);

        return view('admin.responses.forms-picker', [
            'forms' => $forms,
            'mode' => 'statistics',
        ]);
    }

    public function index(Form $form)
    {
        $this->authorize('viewResponses', $form);

        $form->load('questions');
        $responses = $form->responses()->with('answers')->latest('submitted_at')->paginate(20);

        $previewQuestions = $form->questions
            ->sortBy(fn ($q) => $q->type === 'file' ? 0 : 1)
            ->values()
            ->take(5);

        return view('admin.responses.index', compact('form', 'responses', 'previewQuestions'));
    }

    public function show(Form $form, FormResponse $response)
    {
        $this->authorize('viewResponses', $form);
        abort_unless($response->form_id === $form->id, 404);

        $response->load(['answers.question.options']);
        $form->load('questions.options');

        return view('admin.responses.show', compact('form', 'response'));
    }

    public function viewFile(Form $form, FormResponse $response, ResponseAnswer $answer)
    {
        $this->authorize('viewResponses', $form);
        abort_unless($response->form_id === $form->id, 404);
        abort_unless($answer->response_id === $response->id, 404);
        abort_unless($answer->file_path, 404);

        $disk = $answer->storageDisk();
        abort_unless($disk, 404);

        $path = Storage::disk($disk)->path($answer->file_path);
        $mime = Storage::disk($disk)->mimeType($answer->file_path) ?: 'application/octet-stream';

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.basename($answer->file_path).'"',
        ]);
    }

    public function analytics(Form $form)
    {
        $this->authorize('viewResponses', $form);

        $form->load(['questions.options', 'responses.answers']);
        $totalResponses = $form->responses()->count();

        $questionStats = [];
        $chartData = [];

        foreach ($form->questions as $question) {
            $allAnswers = $form->responses->flatMap(fn ($r) => $r->answers->where('question_id', $question->id));

            $stat = [
                'question' => $question,
                'total' => $allAnswers->count(),
                'answers' => $allAnswers,
            ];

            if ($question->hasOptions()) {
                $counts = [];
                foreach ($question->options as $option) {
                    $counts[$option->text] = 0;
                }
                foreach ($allAnswers as $answer) {
                    if ($answer->answer_json) {
                        foreach ($answer->answer_json as $val) {
                            if (isset($counts[$val])) {
                                $counts[$val]++;
                            }
                        }
                    } elseif ($answer->answer_text && isset($counts[$answer->answer_text])) {
                        $counts[$answer->answer_text]++;
                    }
                }
                $stat['option_counts'] = $counts;
                $chartData[] = [
                    'id' => $question->id,
                    'type' => 'options',
                    'labels' => array_keys($counts),
                    'values' => array_values($counts),
                ];
            } elseif ($question->type === 'linear_scale') {
                $scaleCounts = [];
                $min = $question->settings['min'] ?? 1;
                $max = $question->settings['max'] ?? 5;
                for ($i = $min; $i <= $max; $i++) {
                    $scaleCounts[$i] = 0;
                }
                foreach ($allAnswers as $answer) {
                    $val = (int) $answer->answer_text;
                    if (isset($scaleCounts[$val])) {
                        $scaleCounts[$val]++;
                    }
                }
                $stat['scale_counts'] = $scaleCounts;
                $chartData[] = [
                    'id' => $question->id,
                    'type' => 'scale',
                    'labels' => array_map('strval', array_keys($scaleCounts)),
                    'values' => array_values($scaleCounts),
                ];
            } else {
                $stat['text_answers'] = $allAnswers->pluck('answer_text')->filter()->values();
            }

            $questionStats[] = $stat;
        }

        $responsesOverTime = $form->responses()
            ->selectRaw('DATE(submitted_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => $row->date,
                'count' => (int) $row->count,
            ]);

        return view('admin.responses.analytics', compact(
            'form',
            'totalResponses',
            'questionStats',
            'chartData',
            'responsesOverTime',
        ));
    }

    public function export(Form $form): StreamedResponse
    {
        $this->authorize('exportResponses', $form);

        $form->load(['questions', 'responses.answers']);

        $filename = 'form-'.$form->slug.'-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($form) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ResponseExportService::headers($form));

            foreach ($form->responses as $i => $response) {
                fputcsv($handle, ResponseExportService::row($form, $response, $i + 1));
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function destroy(Form $form, FormResponse $response)
    {
        $this->authorize('deleteResponses', $form);
        abort_unless($response->form_id === $form->id, 404);

        $response->delete();

        return back()->with('success', __('app.responses.deleted'));
    }

    private function viewableFormsForResponses()
    {
        $query = Form::query()->withCount('responses')->latest();
        $user = Auth::user();

        if ($user->isSuperAdmin() || $user->canViewAnyResponses()) {
            $query->with('user');
        } else {
            $query->where('user_id', $user->id);
        }

        return $query;
    }
}
