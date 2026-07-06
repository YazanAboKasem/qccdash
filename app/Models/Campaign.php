<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'title',
        'description',
        'logo_path',
        'status',
        'starts_at',
        'ends_at',
        'settings',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'settings' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Campaign $campaign) {
            $campaign->uuid = $campaign->uuid ?? (string) Str::uuid();
        });
    }

    // ─── Relationships ─────────────────────────────────────────

    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    // ─── Scopes ────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRunning($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
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

    public function getTotalResponsesAttribute(): int
    {
        return $this->surveys->sum(function ($survey) {
            return $survey->responses_count ?? $survey->responses()->count();
        });
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }
}
