<?php

namespace App\Repositories;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Builder;

class CampaignRepository extends BaseRepository
{
    protected function model(): string
    {
        return Campaign::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.en')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.ar')) LIKE ?", ["%{$search}%"]);
            });
        }

        return $query;
    }

    public function getActive()
    {
        return $this->query()->active()->with('surveys')->get();
    }

    public function getRunning()
    {
        return $this->query()->running()->with(['surveys.activeQuestions.activeOptions'])->get();
    }

    public function getWithStats()
    {
        return $this->query()
            ->withCount('surveys', 'devices')
            ->with(['surveys' => function ($q) {
                $q->withCount('responses');
            }])
            ->latest()
            ->get();
    }
}
