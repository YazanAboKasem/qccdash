<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AnswerOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'question_id',
        'label',
        'value',
        'icon',
        'color',
        'score',
        'is_correct',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'label' => 'array',
        'score' => 'integer',
        'is_correct' => 'boolean',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (AnswerOption $option) {
            $option->uuid = $option->uuid ?? (string) Str::uuid();

            if (!$option->sort_order) {
                $maxOrder = static::where('question_id', $option->question_id)->max('sort_order');
                $option->sort_order = ($maxOrder ?? 0) + 1;
            }
        });
    }

    // ─── Relationships ─────────────────────────────────────────

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
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

    public function getLocalizedLabelAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->label[$locale] ?? $this->label['en'] ?? '';
    }
}
