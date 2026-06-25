<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Form extends Model
{
    public const TYPE_SURVEY = 'survey';

    public const TYPE_QUIZ = 'quiz';

    public const TYPE_PSYCHOLOGY = 'psychology';

    public const FORM_TYPES = [
        self::TYPE_SURVEY,
        self::TYPE_QUIZ,
        self::TYPE_PSYCHOLOGY,
    ];

    protected $fillable = [
        'user_id', 'title', 'description', 'slug', 'theme_color', 'header_image',
        'form_type', 'is_quiz', 'is_active', 'accept_responses', 'limit_one_response',
        'collect_email', 'shuffle_questions', 'show_progress_bar',
        'confirmation_message', 'settings', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_quiz' => 'boolean',
            'is_active' => 'boolean',
            'accept_responses' => 'boolean',
            'limit_one_response' => 'boolean',
            'collect_email' => 'boolean',
            'shuffle_questions' => 'boolean',
            'show_progress_bar' => 'boolean',
            'settings' => 'array',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Form $form) {
            if (empty($form->slug)) {
                $form->slug = static::generateUniqueSlug($form->title);
            }
        });
    }

    public static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $original.'-'.$count++;
        }

        return $slug;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(FormSection::class)->orderBy('order');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(FormQuestion::class)->orderBy('order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(FormResponse::class);
    }

    public function responseCount(): int
    {
        return $this->responses()->count();
    }

    public function publicUrl(): string
    {
        return route('forms.show', $this->slug);
    }

    public function isPublished(): bool
    {
        return $this->is_active && $this->accept_responses;
    }

    public function resolvedFormType(): string
    {
        if ($this->form_type) {
            return $this->form_type;
        }

        return $this->is_quiz ? self::TYPE_QUIZ : self::TYPE_SURVEY;
    }

    public function isSurvey(): bool
    {
        return $this->resolvedFormType() === self::TYPE_SURVEY;
    }

    public function isQuiz(): bool
    {
        return $this->resolvedFormType() === self::TYPE_QUIZ;
    }

    public function isPsychologyTest(): bool
    {
        return $this->resolvedFormType() === self::TYPE_PSYCHOLOGY;
    }

    public static function syncQuizFlag(string $formType): bool
    {
        return $formType === self::TYPE_QUIZ;
    }

    public static function formTypeLabels(): array
    {
        return collect(self::FORM_TYPES)
            ->mapWithKeys(fn (string $type) => [$type => __("app.forms.types.{$type}")])
            ->all();
    }
}
