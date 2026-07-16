<?php

namespace App\Services;

use App\Repositories\SurveyResponseRepository;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function __construct(
        private SurveyResponseRepository $responseRepository,
    ) {}

    public function getDashboardStats(): array
    {
        return [
            'total_campaigns' => DB::table('campaigns')->whereNull('deleted_at')->count(),
            'active_campaigns' => DB::table('campaigns')->where('status', 'active')->whereNull('deleted_at')->count(),
            'total_surveys' => DB::table('surveys')->whereNull('deleted_at')->count(),
            'total_responses' => DB::table('survey_responses')->count(),
            'today_responses' => DB::table('survey_responses')->whereDate('created_at', today())->count(),
            'active_devices' => DB::table('devices')->where('status', 'active')->count(),
            'online_devices' => DB::table('devices')
                ->where('status', 'active')
                ->where('last_sync_at', '>=', now()->subMinutes(15))
                ->count(),
        ];
    }

    public function getSurveyStats(int $surveyId): array
    {
        $stats = $this->responseRepository->getStatsForSurvey($surveyId);

        // Fetch scores to compute detailed quiz analytics
        $scoresQuery = DB::table('survey_responses')
            ->where('survey_id', $surveyId)
            ->where('status', 'completed')
            ->select('id', DB::raw('(SELECT COALESCE(SUM(score), 0) FROM response_answers WHERE response_id = survey_responses.id) as score'))
            ->get();

        $totalCount = $scoresQuery->count();
        $avgScore = $totalCount > 0 ? $scoresQuery->avg('score') : 0;
        $maxScore = $totalCount > 0 ? $scoresQuery->max('score') : 0;
        $minScore = $totalCount > 0 ? $scoresQuery->min('score') : 0;

        // Score distribution count (e.g. [0 => count, 1 => count, ...])
        $scoreDistribution = [];
        foreach ($scoresQuery as $item) {
            $scoreVal = (int) $item->score;
            $scoreDistribution[$scoreVal] = ($scoreDistribution[$scoreVal] ?? 0) + 1;
        }

        // Pass rate (score >= 7 out of 13)
        $passCount = $scoresQuery->filter(fn($item) => $item->score >= 7)->count();
        $passRate = $totalCount > 0 ? round(($passCount / $totalCount) * 100, 1) : 0;

        $stats['quiz'] = [
            'avg_score' => round($avgScore, 1),
            'max_score' => $maxScore,
            'min_score' => $minScore,
            'pass_rate' => $passRate,
            'pass_count' => $passCount,
            'score_distribution' => $scoreDistribution,
        ];

        return $stats;
    }

    public function getQuestionCorrectRates(int $surveyId): array
    {
        return DB::table('response_answers')
            ->join('survey_responses', 'response_answers.response_id', '=', 'survey_responses.id')
            ->join('questions', 'response_answers.question_id', '=', 'questions.id')
            ->where('survey_responses.survey_id', $surveyId)
            ->where('survey_responses.status', 'completed')
            ->select(
                'questions.id as question_id',
                'questions.text',
                DB::raw('COALESCE(SUM(response_answers.score), 0) as correct_count'),
                DB::raw('COUNT(*) as total_count')
            )
            ->groupBy('questions.id', 'questions.text')
            ->get()
            ->map(function ($row) {
                $row->correct_rate = $row->total_count > 0 ? round(($row->correct_count / $row->total_count) * 100, 1) : 0;
                $row->text = json_decode($row->text, true);
                return $row;
            })
            ->toArray();
    }

    public function getAnswerDistribution(int $surveyId): array
    {
        return $this->responseRepository->getAnswerDistribution($surveyId);
    }

    public function getDailyTrend(int $surveyId, int $days = 30): array
    {
        return $this->responseRepository->getDailyTrend($surveyId, $days);
    }

    public function getRecentActivity(int $limit = 20): array
    {
        return DB::table('activity_logs')
            ->leftJoin('users', 'activity_logs.user_id', '=', 'users.id')
            ->select('activity_logs.*', 'users.name as user_name')
            ->orderByDesc('activity_logs.created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getResponsesForExport(int $surveyId, array $filters = [])
    {
        $query = DB::table('survey_responses')
            ->join('surveys', 'survey_responses.survey_id', '=', 'surveys.id')
            ->leftJoin('devices', 'survey_responses.device_id', '=', 'devices.id')
            ->where('survey_responses.survey_id', $surveyId)
            ->select(
                'survey_responses.uuid',
                'survey_responses.language',
                'survey_responses.status',
                'survey_responses.started_at',
                'survey_responses.completed_at',
                'survey_responses.synced_at',
                'survey_responses.created_at',
                'devices.name as device_name',
                DB::raw('(SELECT COALESCE(SUM(score), 0) FROM response_answers WHERE response_id = survey_responses.id) as score')
            );

        if (!empty($filters['date_from'])) {
            $query->whereDate('survey_responses.created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('survey_responses.created_at', '<=', $filters['date_to']);
        }

        return $query->orderByDesc('survey_responses.created_at')->get();
    }

    public function getHourlyDistribution(int $surveyId): array
    {
        return DB::table('survey_responses')
            ->where('survey_id', $surveyId)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();
    }
}
