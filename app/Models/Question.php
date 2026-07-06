<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'survey_id',
        'type',
        'text',
        'description',
        'sort_order',
        'is_required',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'text' => 'array',
        'description' => 'array',
        'settings' => 'array',
        'sort_order' => 'integer',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Question $question) {
            $question->uuid = $question->uuid ?? (string) Str::uuid();

            if (!$question->sort_order) {
                $maxOrder = static::where('survey_id', $question->survey_id)->max('sort_order');
                $question->sort_order = ($maxOrder ?? 0) + 1;
            }
        });
    }

    // ─── Relationships ─────────────────────────────────────────

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function answerOptions(): HasMany
    {
        return $this->hasMany(AnswerOption::class)->orderBy('sort_order');
    }

    public function activeOptions(): HasMany
    {
        return $this->hasMany(AnswerOption::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function responseAnswers(): HasMany
    {
        return $this->hasMany(ResponseAnswer::class);
    }

    // ─── Scopes ────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    // ─── Accessors ─────────────────────────────────────────────

    public function getLocalizedTextAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->text[$locale] ?? $this->text['en'] ?? '';
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'single_choice' => 'check-circle',
            'multi_choice' => 'list-checks',
            'rating' => 'star',
            'text' => 'type',
            'yes_no' => 'toggle-left',
            default => 'help-circle',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'single_choice' => 'Single Choice',
            'multi_choice' => 'Multiple Choice',
            'rating' => 'Rating Scale',
            'text' => 'Free Text',
            'yes_no' => 'Yes / No',
            default => 'Unknown',
        };
    }
}
