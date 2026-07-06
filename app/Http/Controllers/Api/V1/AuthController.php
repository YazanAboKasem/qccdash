<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Services\DeviceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private DeviceService $deviceService,
    ) {}

    /**
     * Authenticate a device using its identifier and token.
     */
    public function authenticate(Request $request): JsonResponse
    {
        $request->validate([
            'device_identifier' => 'required|string',
            'api_token' => 'required|string',
        ]);

        $device = Device::where('device_identifier', $request->device_identifier)
            ->where('api_token', $request->api_token)
            ->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid device credentials',
            ], 401);
        }

        if ($device->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Device is not active',
            ], 403);
        }

        // Update device info if provided
        if ($request->has('device_info')) {
            $device->update(['device_info' => $request->device_info]);
        }

        $device->recordSync();

        return response()->json([
            'success' => true,
            'data' => [
                'device' => [
                    'uuid' => $device->uuid,
                    'name' => $device->name,
                    'campaign_id' => $device->campaign_id,
                    'settings' => $device->settings,
                ],
                'token' => $device->api_token,
            ],
        ]);
    }

    /**
     * Verify token validity.
     */
    public function verify(Request $request): JsonResponse
    {
        $device = $request->attributes->get('device');

        return response()->json([
            'success' => true,
            'data' => [
                'device' => [
                    'uuid' => $device->uuid,
                    'name' => $device->name,
                    'status' => $device->status,
                ],
            ],
        ]);
    }
}
