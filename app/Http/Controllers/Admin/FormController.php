<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\User;
use App\Notifications\FormCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormController extends Controller
{
    public function index()
    {
        $query = Form::query()->withCount('responses')->latest();

        if (! Auth::user()->canViewAllForms()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->with('user');
        }

        $forms = $query->paginate(12);

        return view('admin.forms.index', compact('forms'));
    }

    public function create()
    {
        return view('admin.forms.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'form_type' => ['required', 'in:'.implode(',', Form::FORM_TYPES)],
        ]);

        $formType = $data['form_type'];

        $form = Form::create([
            'user_id' => Auth::id(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'form_type' => $formType,
            'is_quiz' => Form::syncQuizFlag($formType),
            'published_at' => now(),
        ]);

        if (! Auth::user()->isSuperAdmin()) {
            User::query()
                ->where('role', User::ROLE_SUPER_ADMIN)
                ->each(fn (User $admin) => $admin->notify(new FormCreatedNotification($form, Auth::user())));
        }

        return redirect()->route('admin.forms.edit', $form)
            ->with('success', __('app.forms.created'));
    }

    public function edit(Form $form)
    {
        $this->authorize('update', $form);

        $form->load(['sections', 'questions.options']);

        $builderSections = $form->sections->map(fn ($s) => [
            'id' => $s->id,
            'title' => $s->title,
            'description' => $s->description,
            'order' => $s->order,
            'uid' => 'sec_'.$s->id,
        ])->values();

        $builderQuestions = $form->questions->map(function ($q) {
            $settings = $q->settings ?? ['min' => 1, 'max' => 5, 'min_label' => '', 'max_label' => ''];
            $translations = $settings['translations'] ?? ['ru' => ['title' => '', 'description' => ''], 'en' => ['title' => '', 'description' => '']];

            return [
                'id' => $q->id,
                'uid' => 'q_'.$q->id,
                'section_id' => $q->section_id,
                'type' => $q->type,
                'title' => $q->title === __('app.builder.untitled') ? '' : $q->title,
                'description' => $q->description,
                'translations' => $translations,
                'is_required' => $q->is_required,
                'order' => $q->order,
                'points' => $q->points,
                'settings' => $settings,
                'options' => $q->options->map(fn ($o) => [
                    'id' => $o->id,
                    'uid' => 'o_'.$o->id,
                    'text' => $o->text,
                    'translations' => $o->translations ?? ['ru' => '', 'en' => ''],
                    'is_correct' => $o->is_correct,
                    'score_value' => $o->score_value ?? 0,
                    'order' => $o->order,
                ])->values(),
            ];
        })->values();

        $formTranslations = $form->settings['translations'] ?? [
            'ru' => ['title' => '', 'description' => ''],
            'en' => ['title' => '', 'description' => ''],
        ];

        $builderI18n = [
            'option' => __('app.builder.option'),
            'add_option' => __('app.builder.add_option'),
            'correct' => __('app.builder.correct'),
            'score_value' => __('app.builder.score_value'),
            'min_label' => __('app.builder.min_label'),
            'max_label' => __('app.builder.max_label'),
            'delete_question' => __('app.builder.delete_question'),
            'empty_question_titles' => __('app.builder.empty_question_titles'),
            'save_error' => __('app.builder.save_error'),
            'untitled' => __('app.builder.untitled'),
            'section_prompt' => __('app.builder.section_prompt'),
            'preview' => __('app.builder.preview'),
            'locale_hint' => __('app.builder.locale_hint'),
            'option_ru' => __('app.builder.option_ru'),
            'option_en' => __('app.builder.option_en'),
        ];

        return view('admin.forms.edit', compact('form', 'builderSections', 'builderQuestions', 'builderI18n', 'formTranslations'));
    }

    public function update(Request $request, Form $form)
    {
        $this->authorize('update', $form);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'theme_color' => ['nullable', 'string', 'max:7'],
            'form_type' => ['required', 'in:'.implode(',', Form::FORM_TYPES)],
            'is_active' => ['boolean'],
            'accept_responses' => ['boolean'],
            'limit_one_response' => ['boolean'],
            'collect_email' => ['boolean'],
            'shuffle_questions' => ['boolean'],
            'show_progress_bar' => ['boolean'],
            'confirmation_message' => ['nullable', 'string'],
            'psychology_results' => ['nullable', 'array'],
            'psychology_results.*.min' => ['nullable', 'integer', 'min:0'],
            'psychology_results.*.max' => ['nullable', 'integer', 'min:0'],
            'psychology_results.*.title' => ['nullable', 'string', 'max:255'],
            'psychology_results.*.description' => ['nullable', 'string'],
        ]);

        $formType = $data['form_type'];
        $settings = $form->settings ?? [];

        if ($formType === Form::TYPE_PSYCHOLOGY) {
            $settings['psychology_results'] = collect($data['psychology_results'] ?? [])
                ->filter(fn ($row) => isset($row['min'], $row['max']) && ($row['title'] ?? '') !== '')
                ->values()
                ->all();
        }

        $form->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'theme_color' => $data['theme_color'] ?? '#673ab7',
            'form_type' => $formType,
            'is_quiz' => Form::syncQuizFlag($formType),
            'is_active' => $request->boolean('is_active'),
            'accept_responses' => $request->boolean('accept_responses'),
            'limit_one_response' => $request->boolean('limit_one_response'),
            'collect_email' => $request->boolean('collect_email'),
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'show_progress_bar' => $request->boolean('show_progress_bar'),
            'confirmation_message' => $data['confirmation_message'] ?? null,
            'settings' => $settings,
        ]);

        return back()->with('success', __('app.forms.updated'));
    }

    public function toggleStatus(Request $request, Form $form)
    {
        $this->authorize('update', $form);

        $enabled = $request->boolean('active');

        $form->update([
            'is_active' => $enabled,
            'accept_responses' => $enabled,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'active' => $enabled,
                'label' => $enabled ? __('app.common.active') : __('app.common.closed'),
            ]);
        }

        return back()->with('success', $enabled ? __('app.forms.activated') : __('app.forms.deactivated'));
    }

    public function generateShortLink(Form $form)
    {
        $this->authorize('update', $form);

        $form->ensureShortCode();

        return response()->json([
            'short_url' => $form->shortUrl(),
            'short_code' => $form->short_code,
        ]);
    }

    public function destroy(Form $form)
    {
        $this->authorize('delete', $form);
        $form->delete();

        return redirect()->route('admin.forms.index')
            ->with('success', __('app.forms.deleted'));
    }

    public function duplicate(Form $form)
    {
        $this->authorize('update', $form);

        $newForm = $form->replicate(['slug', 'short_code']);
        $newForm->title = $form->title.' (nusxa)';
        $newForm->slug = Form::generateUniqueSlug($newForm->title);
        $newForm->user_id = Auth::id();
        $newForm->save();

        $sectionMap = [];
        foreach ($form->sections as $section) {
            $newSection = $newForm->sections()->create([
                'title' => $section->title,
                'description' => $section->description,
                'order' => $section->order,
            ]);
            $sectionMap[$section->id] = $newSection->id;
        }

        foreach ($form->questions as $question) {
            $newQuestion = $question->replicate();
            $newQuestion->form_id = $newForm->id;
            $newQuestion->section_id = $question->section_id
                ? ($sectionMap[$question->section_id] ?? null)
                : null;
            $newQuestion->save();

            foreach ($question->options as $option) {
                $newOption = $option->replicate();
                $newOption->question_id = $newQuestion->id;
                $newOption->save();
            }
        }

        return redirect()->route('admin.forms.edit', $newForm)
            ->with('success', __('app.forms.duplicated'));
    }
}
