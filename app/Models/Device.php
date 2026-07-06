<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'device_identifier',
        'api_token',
        'campaign_id',
        'status',
        'last_sync_at',
        'device_info',
        'settings',
    ];

    protected $casts = [
        'device_info' => 'array',
        'settings' => 'array',
        'last_sync_at' => 'datetime',
    ];

    protected $hidden = [
        'api_token',
    ];

    protected static function booted(): void
    {
        static::creating(function (Device $device) {
            $device->uuid = $device->uuid ?? (string) Str::uuid();
        });
    }

    // ─── Relationships ─────────────────────────────────────────

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
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

    // ─── Methods ───────────────────────────────────────────────

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public function recordSync(): void
    {
        $this->update(['last_sync_at' => now()]);
    }

    public function getIsOnlineAttribute(): bool
    {
        if (!$this->last_sync_at) return false;
        return $this->last_sync_at->diffInMinutes(now()) < 15;
    }
}
