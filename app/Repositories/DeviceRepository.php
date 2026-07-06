<?php

namespace App\Repositories;

use App\Models\Device;
use Illuminate\Database\Eloquent\Builder;

class DeviceRepository extends BaseRepository
{
    protected function model(): string
    {
        return Device::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['campaign_id'])) {
            $query->where('campaign_id', $filters['campaign_id']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('device_identifier', 'like', "%{$filters['search']}%");
            });
        }

        return $query;
    }

    public function findByToken(string $token): ?Device
    {
        return $this->query()->where('api_token', $token)->active()->first();
    }

    public function findByIdentifier(string $identifier): ?Device
    {
        return $this->query()->where('device_identifier', $identifier)->first();
    }

    public function getWithSyncStatus()
    {
        return $this->query()
            ->with('campaign')
            ->withCount('responses')
            ->latest('last_sync_at')
            ->get();
    }
}
