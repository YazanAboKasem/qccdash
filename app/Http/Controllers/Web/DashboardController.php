<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Services\ReportService;

class DashboardController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function index()
    {
        $stats = $this->reportService->getDashboardStats();
        $activity = $this->reportService->getRecentActivity(10);
        $campaigns = Campaign::with(['surveys' => function ($query) {
            $query->withCount('responses');
        }])->whereNull('deleted_at')->get();

        return view('dashboard.index', compact('stats', 'activity', 'campaigns'));
    }
}
