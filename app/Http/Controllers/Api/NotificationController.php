<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $notifications = AppNotification::query()
            ->where('company_id', $request->user()->company_id)
            ->latest()
            ->paginate(20);

        return NotificationResource::collection($notifications);
    }

    public function readAll(Request $request): JsonResponse
    {
        AppNotification::query()
            ->where('company_id', $request->user()->company_id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'Semua notifikasi ditandai sudah dibaca.']);
    }
}
