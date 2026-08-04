<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->isSuperAdmin()) {
            if (! $request->routeIs('super-admin.*')) {
                return redirect()->route('super-admin.index');
            }

            return $next($request);
        }

        if (! $user->company_id) {
            $company = Company::create([
                'name' => $user->name ? "{$user->name} Company" : 'My Company',
                'email' => $user->email,
            ]);

            $user->forceFill(['company_id' => $company->id])->save();
            $user->setRelation('company', $company);
        }

        return $next($request);
    }
}
