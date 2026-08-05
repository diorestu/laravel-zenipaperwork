<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'device_type' => ['nullable', 'string', 'in:android,ios,web'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();

        $deviceToken = UserDeviceToken::updateOrCreate(
            [
                'user_id' => $user->id,
                'token' => $data['token'],
            ],
            [
                'company_id' => $user->company_id,
                'device_type' => strtolower($data['device_type'] ?? 'android'),
                'device_name' => $data['device_name'] ?? null,
            ]
        );

        return response()->json([
            'message' => 'Token perangkat berhasil didaftarkan.',
            'device_token' => $deviceToken,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
        ]);

        UserDeviceToken::where('user_id', $request->user()->id)
            ->where('token', $data['token'])
            ->delete();

        return response()->json(['message' => 'Token perangkat berhasil dihapus.']);
    }
}
