<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'survey_id',
        'device_id',
        'language',
        'status',
        'started_at',
        'completed_at',
        'synced_at',
        'survey_version',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'synced_at' => 'datetime',
        'survey_version' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (SurveyResponse $response) {
            $response->uuid = $response->uuid ?? (string) Str::uuid();
        });
    }

    // ─── Relationships ─────────────────────────────────────────

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ResponseAnswer::class, 'response_id');
    }

    // ─── Scopes ────────────────────────────────────────────────

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    // ─── Accessors ─────────────────────────────────────────────

    public function getTotalScoreAttribute(): int
    {
        return $this->answers->sum('score');
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->started_at || !$this->completed_at) return null;
        $seconds = $this->started_at->diffInSeconds($this->completed_at);
        return gmdate('i:s', $seconds);
    }
}
