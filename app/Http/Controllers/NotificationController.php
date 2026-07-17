<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AppNotification::where(function ($query) {
            $query->where('user_id', auth()->id())->orWhere('company_id', auth()->user()->company_id);
        })->latest()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function readAll()
    {
        AppNotification::where(function ($query) {
            $query->where('user_id', auth()->id())->orWhere('company_id', auth()->user()->company_id);
        })->update(['read_at' => now()]);

        return back()->with('success', 'Semua notification ditandai terbaca.');
    }
}
