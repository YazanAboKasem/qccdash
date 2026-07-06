<?php

namespace App\Repositories;

use App\Models\SurveyResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SurveyResponseRepository extends BaseRepository
{
    protected function model(): string
    {
        return SurveyResponse::class;
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['survey_id'])) {
            $query->where('survey_id', $filters['survey_id']);
        }

        if (!empty($filters['device_id'])) {
            $query->where('device_id', $filters['device_id']);
        }

        if (!empty($filters['language'])) {
            $query->where('language', $filters['language']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    public function existsByUuid(string $uuid): bool
    {
        return $this->query()->where('uuid', $uuid)->exists();
    }

    public function createWithAnswers(array $responseData, array $answers): SurveyResponse
    {
        return DB::transaction(function () use ($responseData, $answers) {
            $response = $this->create($responseData);

            foreach ($answers as $answer) {
                $response->answers()->create($answer);
            }

            return $response->load('answers');
        });
    }

    public function getStatsForSurvey(int $surveyId): array
    {
        $base = $this->query()->where('survey_id', $surveyId)->completed();

        return [
            'total' => $base->count(),
            'today' => (clone $base)->today()->count(),
            'this_week' => (clone $base)->thisWeek()->count(),
            'this_month' => (clone $base)->thisMonth()->count(),
            'by_language' => (clone $base)->select('language', DB::raw('count(*) as count'))
                ->groupBy('language')->pluck('count', 'language')->toArray(),
            'by_device' => (clone $base)->select('device_id', DB::raw('count(*) as count'))
                ->groupBy('device_id')->pluck('count', 'device_id')->toArray(),
            'avg_duration' => (clone $base)
                ->whereNotNull('started_at')
                ->whereNotNull('completed_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, started_at, completed_at)) as avg_seconds')
                ->value('avg_seconds'),
        ];
    }

    public function getAnswerDistribution(int $surveyId): array
    {
        return DB::table('response_answers')
            ->join('survey_responses', 'response_answers.response_id', '=', 'survey_responses.id')
            ->join('answer_options', 'response_answers.answer_option_id', '=', 'answer_options.id')
            ->where('survey_responses.survey_id', $surveyId)
            ->where('survey_responses.status', 'completed')
            ->select(
                'response_answers.question_id',
                'response_answers.answer_option_id',
                'answer_options.label',
                'answer_options.color',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('response_answers.question_id', 'response_answers.answer_option_id', 'answer_options.label', 'answer_options.color')
            ->get()
            ->groupBy('question_id')
            ->toArray();
    }

    public function getDailyTrend(int $surveyId, int $days = 30): array
    {
        return $this->query()
            ->where('survey_id', $surveyId)
            ->completed()
            ->where('created_at', '>=', now()->subDays($days))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();
    }
}
