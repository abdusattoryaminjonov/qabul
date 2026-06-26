<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ResponseAnswer extends Model
{
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

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

    public function isImage(): bool
    {
        if (! $this->file_path) {
            return false;
        }

        $extension = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));

        return in_array($extension, self::IMAGE_EXTENSIONS, true);
    }

    public function storageDisk(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($this->file_path)) {
                return $disk;
            }
        }

        return null;
    }

    public function viewUrl(Form $form, FormResponse $response): ?string
    {
        if (! $this->file_path || ! $this->storageDisk()) {
            return null;
        }

        return route('admin.responses.files.view', [$form, $response, $this]);
    }

    public function exportValue(Form $form, FormResponse $response): string
    {
        if ($this->file_path) {
            return $this->viewUrl($form, $response) ?? basename($this->file_path);
        }

        return $this->displayValue();
    }
}
