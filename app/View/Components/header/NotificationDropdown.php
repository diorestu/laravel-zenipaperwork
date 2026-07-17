<?php

namespace App\View\Components\header;

use Closure;
use App\Models\AppNotification;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NotificationDropdown extends Component
{
    public $notifications;
    public $unreadCount;
    public $hasUnread;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        if (auth()->check()) {
            $query = AppNotification::where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->orWhere('company_id', auth()->user()->company_id);
            });

            $this->notifications = (clone $query)->latest()->take(5)->get();
            $this->unreadCount = (clone $query)->whereNull('read_at')->count();
            $this->hasUnread = $this->unreadCount > 0;
        } else {
            $this->notifications = collect();
            $this->unreadCount = 0;
            $this->hasUnread = false;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.header.notification-dropdown');
    }
}
