<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Survey extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'campaign_id',
        'title',
        'description',
        'status',
        'version',
        'settings',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'settings' => 'array',
        'version' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Survey $survey) {
            $survey->uuid = $survey->uuid ?? (string) Str::uuid();
        });
    }

    // ─── Relationships ─────────────────────────────────────────

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('sort_order');
    }

    public function activeQuestions(): HasMany
    {
        return $this->hasMany(Question::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    // ─── Scopes ────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // ─── Accessors ─────────────────────────────────────────────

    public function getLocalizedTitleAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->title[$locale] ?? $this->title['en'] ?? '';
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $this->description[$locale] ?? $this->description['en'] ?? null;
    }

    // ─── Methods ───────────────────────────────────────────────

    public function incrementVersion(): void
    {
        $this->increment('version');
    }

    public function duplicate(): self
    {
        $clone = $this->replicate(['uuid']);
        $clone->uuid = (string) Str::uuid();
        $clone->status = 'draft';
        $clone->version = 1;
        $clone->save();

        foreach ($this->questions as $question) {
            $qClone = $question->replicate(['uuid']);
            $qClone->uuid = (string) Str::uuid();
            $qClone->survey_id = $clone->id;
            $qClone->save();

            foreach ($question->answerOptions as $option) {
                $oClone = $option->replicate(['uuid']);
                $oClone->uuid = (string) Str::uuid();
                $oClone->question_id = $qClone->id;
                $oClone->save();
            }
        }

        return $clone;
    }
}
