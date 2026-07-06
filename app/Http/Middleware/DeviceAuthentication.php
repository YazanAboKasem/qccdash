<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceAuthentication
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication token required',
            ], 401);
        }

        $device = Device::where('api_token', $token)
            ->where('status', 'active')
            ->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive device token',
            ], 401);
        }

        // Attach device to request
        $request->merge(['device' => $device]);
        $request->attributes->set('device', $device);

        return $next($request);
    }
}
