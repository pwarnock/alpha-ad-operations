<?php

namespace Alpha\Reports\Services;

use Alpha\Reports\Contracts\SaaSykitCompatible;
use Alpha\Reports\Traits\UsesReportsConfig;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SaaSykitReportsService implements SaaSykitCompatible
{
    use UsesReportsConfig;

    protected ?object $tenantManager = null;
    protected ?object $subscriptionManager = null;
    protected ?object $currentTenant = null;

    public function __construct()
    {
        $this->initializeSaaSykitServices();
    }

    /**
     * Initialize SaaSykit services if available.
     */
    protected function initializeSaaSykitServices(): void
    {
        // Check if SaaSykit is available through environment detection
        if (!$this->isSaaSykitAvailable()) {
            $this->logReports('info', 'SaaSykit not available, service running in standalone mode');
            return;
        }

        $app = app();

        // Initialize tenant manager
        if (class_exists('\SaaSykit\Tenant\TenantManager') && $app->bound(\SaaSykit\Tenant\TenantManager::class)) {
            $this->tenantManager = $app->make(\SaaSykit\Tenant\TenantManager::class);
            $this->currentTenant = $this->tenantManager->current();
        }

        // Initialize subscription manager
        if (class_exists('\SaaSykit\Subscription\SubscriptionManager') && $app->bound(\SaaSykit\Subscription\SubscriptionManager::class)) {
            $this->subscriptionManager = $app->make(\SaaSykit\Subscription\SubscriptionManager::class);
        }

        $this->logReports('info', 'SaaSykit services initialized', [
            'tenant_manager_available' => $this->tenantManager !== null,
            'subscription_manager_available' => $this->subscriptionManager !== null,
            'current_tenant_id' => $this->getCurrentTenantId(),
        ]);
    }

    /**
     * Check if the current tenant can create reports.
     */
    public function canCreateReports(): bool
    {
        if (!$this->subscriptionManager) {
            return true; // Fallback to true if no subscription manager
        }

        return $this->subscriptionManager->hasFeature('reports.create') ?? true;
    }

    /**
     * Get the maximum number of reports allowed for the current tenant.
     */
    public function getReportLimit(): int
    {
        if (!$this->subscriptionManager) {
            return $this->getReportsConfig('tenant.max_reports_per_tenant', 50);
        }

        $configLimit = $this->getReportsConfig('tenant.max_reports_per_tenant', 50);
        return $this->subscriptionManager->getLimit('reports.max_per_tenant', $configLimit);
    }

    /**
     * Get available features for the current tenant.
     */
    public function getAvailableFeatures(): array
    {
        // Get configured features as defaults
        $defaultFeatures = $this->getReportsConfig('features', [
            'basic_reports' => true,
            'advanced_filters' => false,
            'chart_visualization' => false,
            'excel_export' => false,
            'api_access' => false,
            'real_time_updates' => false,
        ]);

        if (!$this->subscriptionManager) {
            return $defaultFeatures;
        }

        // Override with subscription features
        $subscriptionFeatures = [
            'basic_reports' => $this->subscriptionManager->hasFeature('reports.basic') ?? $defaultFeatures['basic_reports'],
            'advanced_filters' => $this->subscriptionManager->hasFeature('reports.advanced_filters') ?? $defaultFeatures['advanced_filters'],
            'chart_visualization' => $this->subscriptionManager->hasFeature('reports.charts') ?? $defaultFeatures['chart_visualization'],
            'excel_export' => $this->subscriptionManager->hasFeature('reports.excel_export') ?? $defaultFeatures['excel_export'],
            'api_access' => $this->subscriptionManager->hasFeature('reports.api_access') ?? $defaultFeatures['api_access'],
            'real_time_updates' => $this->subscriptionManager->hasFeature('reports.real_time') ?? $defaultFeatures['real_time_updates'],
        ];

        return array_merge($defaultFeatures, $subscriptionFeatures);
    }

    /**
     * Track report usage for billing/analytics.
     */
    public function trackReportUsage(string $reportType, array $metadata = []): void
    {
        $tenantId = $this->getCurrentTenantId();
        
        if (!$tenantId) {
            $this->logReports('warning', 'Attempted to track report usage without active tenant', [
                'report_type' => $reportType,
                'metadata' => $metadata,
            ]);
            return;
        }

        // Use the trait's tracking method
        parent::trackReportUsage($reportType, $metadata);

        // Fire SaaSykit-compatible event if available
        if (class_exists('\SaaSykit\Events\UsageTracked')) {
            event(new \SaaSykit\Events\UsageTracked('reports', $reportType, $metadata));
        }

        // Update usage cache with tenant-aware configuration
        $cachePrefix = $this->getReportsConfig('performance.cache.prefix', 'reports');
        $cacheKey = "{$cachePrefix}.usage.{$tenantId}." . now()->format('Y-m-d');
        $usage = Cache::get($cacheKey, []);
        $usage[] = [
            'type' => $reportType,
            'timestamp' => now()->timestamp,
            'metadata' => $metadata,
        ];
        
        $ttl = $this->getCacheTTL('usage');
        Cache::put($cacheKey, $usage, now()->endOfDay()->addSeconds($ttl));
    }

    /**
     * Get current tenant ID.
     */
    public function getCurrentTenantId(): ?int
    {
        return $this->currentTenant?->id;
    }

    /**
     * Get current tenant instance.
     */
    public function getCurrentTenant(): ?object
    {
        return $this->currentTenant;
    }

    /**
     * Check if a specific feature is available.
     */
    public function hasFeature(string $feature): bool
    {
        $features = $this->getAvailableFeatures();
        return $features[$feature] ?? false;
    }

    /**
     * Get subscription tier for current tenant.
     */
    public function getSubscriptionTier(): string
    {
        if (!$this->subscriptionManager || !$this->currentTenant) {
            return 'basic';
        }

        return $this->subscriptionManager->getTier($this->currentTenant) ?? 'basic';
    }

    /**
     * Check if current tenant has reached report limit.
     */
    public function hasReachedReportLimit(): bool
    {
        if (!$this->currentTenant) {
            return false;
        }

        $currentCount = $this->getCurrentReportCount();
        $limit = $this->getReportLimit();

        return $currentCount >= $limit;
    }

    /**
     * Get current report count for tenant.
     */
    protected function getCurrentReportCount(): int
    {
        if (!class_exists('\Alpha\Reports\Models\SavedReport')) {
            return 0;
        }

        return \Alpha\Reports\Models\SavedReport::where('tenant_id', $this->getCurrentTenantId())
            ->count();
    }

    /**
     * Get remaining report slots for current tenant.
     */
    public function getRemainingReportSlots(): int
    {
        $limit = $this->getReportLimit();
        $current = $this->getCurrentReportCount();
        
        return max(0, $limit - $current);
    }

    /**
     * Check if tenant can use advanced filtering.
     */
    public function canUseAdvancedFilters(): bool
    {
        return $this->hasFeature('advanced_filters');
    }

    /**
     * Check if tenant can export to Excel.
     */
    public function canExportToExcel(): bool
    {
        return $this->hasFeature('excel_export');
    }

    /**
     * Check if tenant can use API access.
     */
    public function canUseApiAccess(): bool
    {
        return $this->hasFeature('api_access');
    }

    /**
     * Check if tenant can use real-time updates.
     */
    public function canUseRealTimeUpdates(): bool
    {
        return $this->hasFeature('real_time_updates');
    }

    /**
     * Get feature restrictions for current tenant.
     */
    public function getFeatureRestrictions(): array
    {
        $features = $this->getAvailableFeatures();
        $restrictions = [];

        foreach ($features as $feature => $available) {
            if (!$available) {
                $restrictions[$feature] = $this->getRequiredPlanForFeature($feature);
            }
        }

        return $restrictions;
    }

    /**
     * Get required subscription plan for a feature.
     */
    protected function getRequiredPlanForFeature(string $feature): string
    {
        $featureRequirements = [
            'advanced_filters' => 'professional',
            'chart_visualization' => 'professional',
            'excel_export' => 'professional',
            'api_access' => 'enterprise',
            'real_time_updates' => 'enterprise',
        ];

        return $featureRequirements[$feature] ?? 'enterprise';
    }

    /**
     * Refresh tenant and subscription data.
     */
    public function refresh(): void
    {
        if ($this->tenantManager) {
            $this->currentTenant = $this->tenantManager->current();
        }

        // Clear feature cache
        Cache::tags(['reports', 'features'])->flush();
    }

    /**
     * Check if SaaSykit is available.
     */
    public function isSaaSykitAvailable(): bool
    {
        return $this->tenantManager !== null && $this->subscriptionManager !== null;
    }
}