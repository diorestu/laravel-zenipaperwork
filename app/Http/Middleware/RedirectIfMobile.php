<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfMobile
{
    public function handle(Request $request, Closure $next): Response
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());

        if ($agent->isMobile() || $agent->isTablet()) {
            // Do not redirect if already accessing mobile pages, API routes, or logout
            if (! $request->is('mobile*') && ! $request->is('api*') && ! $request->is('logout')) {
                // Support desktop override via ?desktop=1 or cookie
                if ($request->query('desktop') === '1' || $request->cookie('prefer_desktop') === '1') {
                    if ($request->query('desktop') === '1') {
                        cookie()->queue('prefer_desktop', '1', 60 * 24 * 30);
                    }

                    return $next($request);
                }

                if ($request->query('desktop') === '0') {
                    cookie()->queue(cookie()->forget('prefer_desktop'));
                }

                return redirect()->route('mobile.app');
            }
        }

        return $next($request);
    }
}
