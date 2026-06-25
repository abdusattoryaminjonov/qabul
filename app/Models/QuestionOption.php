<?php

namespace App\Models;

use App\Support\FormTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionOption extends Model
{
    protected $fillable = ['question_id', 'text', 'translations', 'is_correct', 'score_value', 'order'];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'translations' => 'array',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(FormQuestion::class, 'question_id');
    }

    public function localizedText(?string $locale = null): string
    {
        return FormTranslations::resolveText($this->translations, $this->text, $locale);
    }
}
