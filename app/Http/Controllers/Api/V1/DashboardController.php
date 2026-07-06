<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private ReportService $reportService,
    ) {}

    /**
     * Overall dashboard statistics.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->reportService->getDashboardStats(),
        ]);
    }

    /**
     * Recent activity log.
     */
    public function recentActivity(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 20);

        return response()->json([
            'success' => true,
            'data' => $this->reportService->getRecentActivity($limit),
        ]);
    }

    /**
     * Statistics for a specific survey.
     */
    public function surveyStats(int $survey): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->reportService->getSurveyStats($survey),
        ]);
    }

    /**
     * Answer distribution for a survey.
     */
    public function answerDistribution(int $survey): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->reportService->getAnswerDistribution($survey),
        ]);
    }

    /**
     * Daily response trend for a survey.
     */
    public function dailyTrend(Request $request, int $survey): JsonResponse
    {
        $days = $request->integer('days', 30);

        return response()->json([
            'success' => true,
            'data' => $this->reportService->getDailyTrend($survey, $days),
        ]);
    }

    /**
     * Hourly response distribution for a survey.
     */
    public function hourlyDistribution(int $survey): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->reportService->getHourlyDistribution($survey),
        ]);
    }

    /**
     * Export responses for a survey.
     */
    public function export(Request $request, int $survey): JsonResponse
    {
        $filters = $request->only(['date_from', 'date_to']);
        $responses = $this->reportService->getResponsesForExport($survey, $filters);

        return response()->json([
            'success' => true,
            'data' => $responses,
        ]);
    }
}
