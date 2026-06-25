<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormQuestion;
use App\Models\FormSection;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormBuilderController extends Controller
{
    public function saveStructure(Request $request, Form $form)
    {
        $this->authorize('update', $form);

        $data = $request->validate([
            'sections' => ['nullable', 'array'],
            'sections.*.id' => ['nullable'],
            'sections.*.title' => ['nullable', 'string'],
            'sections.*.description' => ['nullable', 'string'],
            'sections.*.order' => ['required', 'integer'],
            'questions' => ['required', 'array'],
            'questions.*.id' => ['nullable'],
            'questions.*.section_id' => ['nullable'],
            'questions.*.type' => ['required', 'string'],
            'questions.*.title' => ['required', 'string'],
            'questions.*.description' => ['nullable', 'string'],
            'questions.*.is_required' => ['boolean'],
            'questions.*.order' => ['required', 'integer'],
            'questions.*.points' => ['nullable', 'integer'],
            'questions.*.settings' => ['nullable', 'array'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.options.*.text' => ['required', 'string'],
            'questions.*.options.*.is_correct' => ['boolean'],
            'questions.*.options.*.score_value' => ['nullable', 'integer'],
            'questions.*.options.*.translations' => ['nullable', 'array'],
            'questions.*.options.*.order' => ['required', 'integer'],
            'form' => ['nullable', 'array'],
            'form.title' => ['nullable', 'string'],
            'form.description' => ['nullable', 'string'],
            'form.translations' => ['nullable', 'array'],
        ]);

        DB::transaction(function () use ($form, $data) {
            if (! empty($data['form'])) {
                $settings = $form->settings ?? [];
                if (! empty($data['form']['translations'])) {
                    $settings['translations'] = $data['form']['translations'];
                }
                $form->update([
                    'title' => $data['form']['title'] ?? $form->title,
                    'description' => $data['form']['description'] ?? $form->description,
                    'settings' => $settings,
                ]);
            }

            $existingSectionIds = $form->sections()->pluck('id')->toArray();
            $newSectionIds = [];
            $sectionTempMap = [];

            foreach ($data['sections'] ?? [] as $sectionData) {
                if (! empty($sectionData['id']) && in_array($sectionData['id'], $existingSectionIds)) {
                    $section = FormSection::find($sectionData['id']);
                    $section->update([
                        'title' => $sectionData['title'] ?? null,
                        'description' => $sectionData['description'] ?? null,
                        'order' => $sectionData['order'],
                    ]);
                    $newSectionIds[] = $section->id;
                    $sectionTempMap[$sectionData['id']] = $section->id;
                } else {
                    $section = $form->sections()->create([
                        'title' => $sectionData['title'] ?? null,
                        'description' => $sectionData['description'] ?? null,
                        'order' => $sectionData['order'],
                    ]);
                    $newSectionIds[] = $section->id;
                    if (! empty($sectionData['id'])) {
                        $sectionTempMap[$sectionData['id']] = $section->id;
                    }
                }
            }

            FormSection::where('form_id', $form->id)
                ->whereNotIn('id', $newSectionIds)
                ->delete();

            $existingQuestionIds = $form->questions()->pluck('id')->toArray();
            $newQuestionIds = [];

            foreach ($data['questions'] as $questionData) {
                $sectionId = null;
                if (! empty($questionData['section_id'])) {
                    $sectionId = $sectionTempMap[$questionData['section_id']]
                        ?? (is_numeric($questionData['section_id']) ? (int) $questionData['section_id'] : null);
                }

                $attrs = [
                    'section_id' => $sectionId,
                    'type' => $questionData['type'],
                    'title' => $questionData['title'],
                    'description' => $questionData['description'] ?? null,
                    'is_required' => $questionData['is_required'] ?? false,
                    'order' => $questionData['order'],
                    'points' => $questionData['points'] ?? 0,
                    'settings' => $questionData['settings'] ?? null,
                ];

                if (! empty($questionData['id']) && in_array($questionData['id'], $existingQuestionIds)) {
                    $question = FormQuestion::find($questionData['id']);
                    $question->update($attrs);
                } else {
                    $question = $form->questions()->create($attrs);
                }

                $newQuestionIds[] = $question->id;

                if ($question->hasOptions()) {
                    $existingOptionIds = $question->options()->pluck('id')->toArray();
                    $newOptionIds = [];

                    foreach ($questionData['options'] ?? [] as $optionData) {
                        if (! empty($optionData['id']) && in_array($optionData['id'], $existingOptionIds)) {
                            $option = QuestionOption::find($optionData['id']);
                            $option->update([
                                'text' => $optionData['text'],
                                'translations' => $optionData['translations'] ?? null,
                                'is_correct' => $optionData['is_correct'] ?? false,
                                'score_value' => $optionData['score_value'] ?? 0,
                                'order' => $optionData['order'],
                            ]);
                            $newOptionIds[] = $option->id;
                        } else {
                            $option = $question->options()->create([
                                'text' => $optionData['text'],
                                'translations' => $optionData['translations'] ?? null,
                                'is_correct' => $optionData['is_correct'] ?? false,
                                'score_value' => $optionData['score_value'] ?? 0,
                                'order' => $optionData['order'],
                            ]);
                            $newOptionIds[] = $option->id;
                        }
                    }

                    QuestionOption::where('question_id', $question->id)
                        ->whereNotIn('id', $newOptionIds)
                        ->delete();
                } else {
                    $question->options()->delete();
                }
            }

            FormQuestion::where('form_id', $form->id)
                ->whereNotIn('id', $newQuestionIds)
                ->delete();
        });

        $form->load(['sections', 'questions.options']);

        return response()->json([
            'success' => true,
            'message' => 'Forma saqlandi.',
            'sections' => $form->sections->map(fn ($s) => [
                'id' => $s->id,
                'title' => $s->title,
                'description' => $s->description,
                'order' => $s->order,
                'uid' => 'sec_'.$s->id,
            ]),
            'questions' => $form->questions->map(fn ($q) => [
                'id' => $q->id,
                'uid' => 'q_'.$q->id,
                'section_id' => $q->section_id,
                'type' => $q->type,
                'title' => $q->title,
                'description' => $q->description,
                'is_required' => $q->is_required,
                'order' => $q->order,
                'points' => $q->points,
                'settings' => $q->settings ?? ['min' => 1, 'max' => 5, 'min_label' => '', 'max_label' => ''],
                'options' => $q->options->map(fn ($o) => [
                    'id' => $o->id,
                    'uid' => 'o_'.$o->id,
                    'text' => $o->text,
                    'translations' => $o->translations ?? ['ru' => '', 'en' => ''],
                    'is_correct' => $o->is_correct,
                    'score_value' => $o->score_value ?? 0,
                    'order' => $o->order,
                ])->values(),
            ]),
        ]);
    }
}
