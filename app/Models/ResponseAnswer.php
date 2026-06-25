<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResponseAnswer extends Model
{
    protected $fillable = [
        'response_id', 'question_id', 'answer_text', 'answer_json', 'file_path',
    ];

    protected function casts(): array
    {
        return ['answer_json' => 'array'];
    }

    public function response(): BelongsTo
    {
        return $this->belongsTo(FormResponse::class, 'response_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(FormQuestion::class, 'question_id');
    }

    public function displayValue(): string
    {
        if ($this->file_path) {
            return basename($this->file_path);
        }

        if ($this->answer_json) {
            return implode(', ', $this->answer_json);
        }

        return $this->answer_text ?? '';
    }
}
