<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormQuestion extends Model
{
    public const TYPE_KEYS = [
        'short_text', 'paragraph', 'multiple_choice', 'checkbox',
        'dropdown', 'linear_scale', 'date', 'time', 'file',
    ];

    public static function typeKeys(): array
    {
        return self::TYPE_KEYS;
    }

    public static function typeLabels(): array
    {
        return collect(self::TYPE_KEYS)
            ->mapWithKeys(fn (string $key) => [$key => __("app.question_types.{$key}")])
            ->all();
    }

    protected $fillable = [
        'form_id', 'section_id', 'type', 'title', 'description',
        'is_required', 'order', 'points', 'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(FormSection::class, 'section_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class, 'question_id')->orderBy('order');
    }

    public function hasOptions(): bool
    {
        return in_array($this->type, ['multiple_choice', 'checkbox', 'dropdown']);
    }
}
