<?php

namespace App\Support;

use App\Models\AppNotification;
use App\Models\User;

class ActivityNotifier
{
    public static function record(User $actor, string $title, ?string $body = null): void
    {
        if (! $actor->company_id) {
            return;
        }

        AppNotification::create([
            'company_id' => $actor->company_id,
            'user_id' => $actor->id,
            'title' => $title,
            'body' => $body,
        ]);
    }
}
