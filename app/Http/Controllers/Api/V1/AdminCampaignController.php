<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Services\CampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminCampaignController extends Controller
{
    public function __construct(
        private CampaignService $campaignService,
    ) {}

    /**
     * List all campaigns with stats.
     */
    public function index(Request $request): JsonResponse
    {
        $campaigns = $this->campaignService->getWithStats();

        return response()->json([
            'success' => true,
            'data' => $campaigns,
        ]);
    }

    /**
     * Create a new campaign.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.ar' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'status' => 'nullable|string|in:draft,active,paused,completed',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'settings' => 'nullable|array',
            'logo' => 'nullable|image|max:2048',
        ]);

        $logo = $request->file('logo');
        unset($validated['logo']);

        $campaign = $this->campaignService->create($validated, $logo);

        return response()->json([
            'success' => true,
            'data' => $campaign,
            'message' => 'Campaign created successfully.',
        ], 201);
    }

    /**
     * Show a single campaign.
     */
    public function show(int $id): JsonResponse
    {
        $campaign = $this->campaignService->find($id);

        $campaign->load(['surveys' => function ($q) {
            $q->withCount('responses');
        }, 'devices']);

        return response()->json([
            'success' => true,
            'data' => $campaign,
        ]);
    }

    /**
     * Update a campaign.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $campaign = $this->campaignService->find($id);

        $validated = $request->validate([
            'title' => 'sometimes|array',
            'title.en' => 'required_with:title|string|max:255',
            'title.ar' => 'nullable|string|max:255',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'status' => 'nullable|string|in:draft,active,paused,completed',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'settings' => 'nullable|array',
            'logo' => 'nullable|image|max:2048',
        ]);

        $logo = $request->file('logo');
        unset($validated['logo']);

        $campaign = $this->campaignService->update($campaign, $validated, $logo);

        return response()->json([
            'success' => true,
            'data' => $campaign,
            'message' => 'Campaign updated successfully.',
        ]);
    }

    /**
     * Delete a campaign.
     */
    public function destroy(int $id): JsonResponse
    {
        $campaign = $this->campaignService->find($id);
        $this->campaignService->delete($campaign);

        return response()->json([
            'success' => true,
            'message' => 'Campaign deleted successfully.',
        ]);
    }

    /**
     * Update campaign status.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:draft,active,paused,completed',
        ]);

        $campaign = $this->campaignService->find($id);
        $campaign = $this->campaignService->updateStatus($campaign, $request->status);

        return response()->json([
            'success' => true,
            'data' => $campaign,
            'message' => 'Campaign status updated.',
        ]);
    }
}
