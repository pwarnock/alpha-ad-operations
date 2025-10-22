<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Providers\TenancyServiceProvider;
use App\Models\Tenant;

class InitializeTenancy
{
    public function handle(Request $request, Closure $next)
    {
        // Skip tenancy for admin panel routes
        if ($request->is('admin*')) {
            return $next($request);
        }

        $tenant = $this->resolveTenant($request);

        if ($tenant) {
            // Set tenant context for the request
            app()->instance('current_tenant', $tenant);
            session(['tenant_id' => $tenant->id]);

            // You can add database switching logic here if needed
            // $this->switchDatabase($tenant);
        }

        return $next($request);
    }

    private function resolveTenant(Request $request): ?Tenant
    {
        $hostname = $request->getHost();
        
        // Try domain-based resolution
        $tenant = Tenant::where('domain', $hostname)->active()->first();
        
        // Fallback to session for admin access
        if (!$tenant && session('tenant_id')) {
            $tenant = Tenant::find(session('tenant_id'));
        }

        return $tenant?->is_active ? $tenant : null;
    }

    private function switchDatabase(Tenant $tenant): void
    {
        // Implement database switching logic here
        // This would connect to the tenant's specific database
        if ($tenant->database) {
            config(['database.connections.tenant.database' => $tenant->database]);
        }
    }
}