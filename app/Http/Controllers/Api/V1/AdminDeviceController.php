<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\DeviceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDeviceController extends Controller
{
    public function __construct(
        private DeviceService $deviceService,
    ) {}

    /**
     * List all devices with sync status.
     */
    public function index(): JsonResponse
    {
        $devices = $this->deviceService->getWithSyncStatus();

        return response()->json([
            'success' => true,
            'data' => $devices,
        ]);
    }

    /**
     * Register a new device.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'device_identifier' => 'required|string|max:255|unique:devices,device_identifier',
            'campaign_id' => 'required|integer|exists:campaigns,id',
            'status' => 'nullable|string|in:active,inactive,maintenance',
            'device_info' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);

        $device = $this->deviceService->register($validated);

        // Return token only on creation — this is the only time it's visible
        return response()->json([
            'success' => true,
            'data' => [
                'device' => $device,
                'api_token' => $device->api_token,
            ],
            'message' => 'Device registered. Save the API token — it will not be shown again.',
        ], 201);
    }

    /**
     * Show a single device.
     */
    public function show(int $id): JsonResponse
    {
        $device = $this->deviceService->find($id);
        $device->load('campaign');
        $device->loadCount('responses');

        return response()->json([
            'success' => true,
            'data' => $device,
        ]);
    }

    /**
     * Update device details.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $device = $this->deviceService->find($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'device_identifier' => 'sometimes|string|max:255|unique:devices,device_identifier,' . $device->id,
            'campaign_id' => 'sometimes|integer|exists:campaigns,id',
            'device_info' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);

        $device = $this->deviceService->update($device, $validated);

        return response()->json([
            'success' => true,
            'data' => $device,
            'message' => 'Device updated successfully.',
        ]);
    }

    /**
     * Delete a device.
     */
    public function destroy(int $id): JsonResponse
    {
        $device = $this->deviceService->find($id);
        $this->deviceService->delete($device);

        return response()->json([
            'success' => true,
            'message' => 'Device deleted successfully.',
        ]);
    }

    /**
     * Regenerate the API token for a device.
     */
    public function regenerateToken(int $id): JsonResponse
    {
        $device = $this->deviceService->find($id);
        $device = $this->deviceService->regenerateToken($device);

        return response()->json([
            'success' => true,
            'data' => [
                'api_token' => $device->api_token,
            ],
            'message' => 'Token regenerated. Save the new token — it will not be shown again.',
        ]);
    }

    /**
     * Update device status.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:active,inactive,maintenance',
        ]);

        $device = $this->deviceService->find($id);
        $device = $this->deviceService->updateStatus($device, $request->status);

        return response()->json([
            'success' => true,
            'data' => $device,
            'message' => 'Device status updated.',
        ]);
    }
}
