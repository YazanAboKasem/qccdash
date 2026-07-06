<?php

namespace App\Repositories;

use App\Models\Survey;
use Illuminate\Database\Eloquent\Builder;

class SurveyRepository extends BaseRepository
{
    protected function model(): string
    {
        return Survey::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['campaign_id'])) {
            $query->where('campaign_id', $filters['campaign_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    public function getActiveForCampaign(int $campaignId): ?Survey
    {
        return $this->query()
            ->where('campaign_id', $campaignId)
            ->active()
            ->with(['activeQuestions.activeOptions'])
            ->first();
    }

    public function getWithQuestions(int $id): Survey
    {
        return $this->query()
            ->with(['questions.answerOptions', 'campaign'])
            ->findOrFail($id);
    }

    public function getFullSurveyByUuid(string $uuid): ?Survey
    {
        return $this->query()
            ->where('uuid', $uuid)
            ->active()
            ->with([
                'campaign',
                'activeQuestions' => function ($q) {
                    $q->with('activeOptions');
                },
            ])
            ->first();
    }
}
