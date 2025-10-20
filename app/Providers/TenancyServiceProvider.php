<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Tenant;

class TenancyServiceProvider extends ServiceProvider
{
    public const TENANCY_INITIALIZER = \App\Http\Middleware\InitializeTenancy::class;

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register tenant resolution middleware
        $this->app->singleton('tenant', function () {
            return $this->resolveTenant();
        });
    }

    private function resolveTenant(): ?Tenant
    {
        // Resolve tenant from domain, subdomain, or session
        $hostname = request()->getHost();
        
        // Try to resolve by domain
        $tenant = Tenant::where('domain', $hostname)->active()->first();
        
        if (!$tenant) {
            // Try to resolve from session (for admin access)
            $tenantId = session('tenant_id');
            if ($tenantId) {
                $tenant = Tenant::find($tenantId);
            }
        }

        return $tenant?->is_active ? $tenant : null;
    }
}