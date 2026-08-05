<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Super Admin is exempt from subscription checks
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        // If not logged in or doesn't have a company yet, skip
        if (! $user || ! $user->company_id) {
            return $next($request);
        }

        $company = $user->company;

        if (! $company) {
            return $next($request);
        }

        // Determine if they are on trial or have an active paid plan
        $hasActiveSubscription = $company->active_plan && $company->subscription_ends_at && $company->subscription_ends_at->isFuture();
        $hasActiveTrial = $company->onTrial();

        if (! $hasActiveSubscription && ! $hasActiveTrial) {
            // Define routes allowed even when subscription is expired/inactive
            $allowedRoutes = [
                'settings.billing',
                'settings.billing.show',
                'billing.store',
                'logout',
                'api.billing.plans',
                'api.billing.submissions.index',
                'api.billing.submissions.store',
                'api.billing.submissions.show',
                'api.logout',
            ];

            if ($request->routeIs($allowedRoutes)) {
                return $next($request);
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Masa trial atau langganan Anda telah habis. Silakan lakukan pembayaran untuk melanjutkan.',
                    'billing_url' => route('settings.billing'),
                ], 402);
            }

            return redirect()->route('settings.billing')->with('warning', 'Masa trial atau langganan Anda telah habis. Silakan lakukan pembayaran untuk melanjutkan.');
        }

        return $next($request);
    }
}
