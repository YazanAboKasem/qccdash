<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BatchSyncRequest;
use App\Http\Requests\Api\SubmitResponseRequest;
use App\Services\SyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function __construct(
        private SyncService $syncService,
    ) {}

    /**
     * Submit a single survey response.
     */
    public function submit(SubmitResponseRequest $request): JsonResponse
    {
        $device = $request->attributes->get('device');
        $data = $request->validated();
        $data['device_id'] = $device->id;

        $result = $this->syncService->submitResponse($data);

        $statusCode = match ($result['status']) {
            'success' => 201,
            'duplicate' => 200,
            default => 500,
        };

        return response()->json([
            'success' => $result['status'] !== 'error',
            'data' => $result,
        ], $statusCode);
    }

    /**
     * Batch sync multiple responses (offline sync).
     */
    public function batchSync(BatchSyncRequest $request): JsonResponse
    {
        $device = $request->attributes->get('device');
        $data = $request->validated();

        $result = $this->syncService->batchSync($data['responses'], $device->id);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
