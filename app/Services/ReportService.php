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
        return $this->responseRepository->getStatsForSurvey($surveyId);
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
                'devices.name as device_name'
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
