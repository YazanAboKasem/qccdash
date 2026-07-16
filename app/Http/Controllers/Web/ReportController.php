<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function show(Survey $survey)
    {
        $survey->load('questions');
        $surveyId = $survey->id;

        $stats = $this->reportService->getSurveyStats($surveyId);
        $distribution = $this->reportService->getAnswerDistribution($surveyId);
        $dailyTrend = $this->reportService->getDailyTrend($surveyId, 30);
        $hourlyDistribution = $this->reportService->getHourlyDistribution($surveyId);
        $responses = $this->reportService->getResponsesForExport($surveyId);
        $correctRates = $this->reportService->getQuestionCorrectRates($surveyId);

        return view('reports.show', compact('survey', 'stats', 'distribution', 'dailyTrend', 'hourlyDistribution', 'responses', 'correctRates'));
    }

    public function export(Survey $survey, Request $request)
    {
        $surveyId = $survey->id;
        $filters = $request->only(['date_from', 'date_to']);
        $responses = $this->reportService->getResponsesForExport($surveyId, $filters);

        $fileName = 'survey_report_' . $survey->uuid . '_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($responses) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel Arabic/UTF-8 rendering
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['Response UUID', 'Language', 'Score', 'Status', 'Started At', 'Completed At', 'Synced At', 'Submitted At', 'Device Name']);

            foreach ($responses as $response) {
                fputcsv($file, [
                    $response->uuid,
                    $response->language,
                    $response->score,
                    $response->status,
                    $response->started_at,
                    $response->completed_at,
                    $response->synced_at,
                    $response->created_at,
                    $response->device_name ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
