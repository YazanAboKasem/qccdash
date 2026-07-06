<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\SurveyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function __construct(
        private SurveyService $surveyService,
    ) {}

    /**
     * Get the active survey for the authenticated device's campaign.
     */
    public function active(Request $request): JsonResponse
    {
        $device = $request->attributes->get('device');

        $survey = $this->surveyService->getActiveSurveyForDevice($device->campaign_id);

        if (! $survey) {
            return response()->json([
                'success' => false,
                'message' => 'No active survey found for this campaign.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $survey,
        ]);
    }

    /**
     * Get full survey details by UUID.
     */
    public function show(string $uuid): JsonResponse
    {
        $survey = app(\App\Repositories\SurveyRepository::class)->getFullSurveyByUuid($uuid);

        if (! $survey) {
            return response()->json([
                'success' => false,
                'message' => 'Survey not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $survey->id,
                'uuid' => $survey->uuid,
                'title' => $survey->title,
                'description' => $survey->description,
                'version' => $survey->version,
                'settings' => $survey->settings,
                'campaign' => [
                    'uuid' => $survey->campaign->uuid,
                    'title' => $survey->campaign->title,
                    'logo_url' => $survey->campaign->logo_url,
                    'settings' => $survey->campaign->settings,
                ],
                'questions' => $survey->activeQuestions->map(function ($q) {
                    return [
                        'uuid' => $q->uuid,
                        'type' => $q->type,
                        'text' => $q->text,
                        'description' => $q->description,
                        'is_required' => $q->is_required,
                        'settings' => $q->settings,
                        'options' => $q->activeOptions->map(function ($o) {
                            return [
                                'uuid' => $o->uuid,
                                'label' => $o->label,
                                'value' => $o->value,
                                'icon' => $o->icon,
                                'color' => $o->color,
                                'score' => $o->score,
                            ];
                        })->values(),
                    ];
                })->values(),
            ],
        ]);
    }
}
