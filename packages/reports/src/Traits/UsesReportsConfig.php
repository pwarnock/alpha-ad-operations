<?php

namespace Alpha\Reports\Traits;

use Alpha\Reports\Services\ConfigurationManager;
use Alpha\Reports\Services\EnvironmentDetectionService;
use Illuminate\Support\Facades\App;

trait UsesReportsConfig
{
    /**
     * Get the configuration manager instance.
     */
    protected function getConfigManager(): ConfigurationManager
    {
        return App::make(ConfigurationManager::class);
    }

    /**
     * Get the environment detection service instance.
     */
    protected function getEnvironmentDetection(): EnvironmentDetectionService
    {
        return App::make(EnvironmentDetectionService::class);
    }

    /**
     * Get a configuration value with runtime overrides.
     */
    protected function getReportsConfig(string $key, $default = null)
    {
        return $this->getConfigManager()->get($key, $default);
    }

    /**
     * Check if a feature is enabled.
     */
    protected function isFeatureEnabled(string $feature): bool
    {
        return $this->getConfigManager()->isFeatureEnabled($feature);
    }

    /**
     * Check if SaaSykit is available.
     */
    protected function isSaaSykitAvailable(): bool
    {
        return $this->getEnvironmentDetection()->isSaaSykitAvailable();
    }

    /**
     * Check if Laravel Tenancy is available.
     */
    protected function isLaravelTenancyAvailable(): bool
    {
        return $this->getEnvironmentDetection()->isLaravelTenancyAvailable();
    }

    /**
     * Get current tenant ID if available.
     */
    protected function getCurrentTenantId(): ?int
    {
        if (!$this->isSaaSykitAvailable() && !$this->isLaravelTenancyAvailable()) {
            return null;
        }

        // Try to get from SaaSykit first
        if ($this->isSaaSykitAvailable()) {
            $saasykitService = App::make(\Alpha\Reports\Services\SaaSykitReportsService::class);
            return $saasykitService->getCurrentTenantId();
        }

        // Fallback to Laravel Tenancy
        // This would need to be implemented based on the specific tenancy package
        return null;
    }

    /**
     * Get effective tenant configuration.
     */
    protected function getTenantConfig(): array
    {
        return $this->getConfigManager()->getTenantConfig($this->getCurrentTenantId());
    }

    /**
     * Check if current tenant can create reports.
     */
    protected function canCreateReports(): bool
    {
        if (!$this->isFeatureEnabled('basic_reports')) {
            return false;
        }

        if ($this->isSaaSykitAvailable()) {
            $saasykitService = App::make(\Alpha\Reports\Services\SaaSykitReportsService::class);
            return $saasykitService->canCreateReports();
        }

        return true;
    }

    /**
     * Get report limit for current tenant.
     */
    protected function getReportLimit(): int
    {
        if ($this->isSaaSykitAvailable()) {
            $saasykitService = App::make(\Alpha\Reports\Services\SaaSykitReportsService::class);
            return $saasykitService->getReportLimit();
        }

        return $this->getReportsConfig('tenant.max_reports_per_tenant', 50);
    }

    /**
     * Check if current tenant has reached report limit.
     */
    protected function hasReachedReportLimit(): bool
    {
        if (!$this->isSaaSykitAvailable()) {
            return false;
        }

        $saasykitService = App::make(\Alpha\Reports\Services\SaaSykitReportsService::class);
        return $saasykitService->hasReachedReportLimit();
    }

    /**
     * Get available features for current tenant.
     */
    protected function getAvailableFeatures(): array
    {
        if ($this->isSaaSykitAvailable()) {
            $saasykitService = App::make(\Alpha\Reports\Services\SaaSykitReportsService::class);
            return $saasykitService->getAvailableFeatures();
        }

        // Return configured features for standalone mode
        return $this->getReportsConfig('features', []);
    }

    /**
     * Check if a specific feature is available for current tenant.
     */
    protected function hasFeature(string $feature): bool
    {
        $features = $this->getAvailableFeatures();
        return $features[$feature] ?? false;
    }

    /**
     * Get cache TTL for a specific type.
     */
    protected function getCacheTTL(string $type = 'default'): int
    {
        return $this->getReportsConfig("performance.cache.{$type}_ttl", 
            $this->getReportsConfig('performance.cache.default_ttl', 3600));
    }

    /**
     * Get export configuration.
     */
    protected function getExportConfig(): array
    {
        return $this->getReportsConfig('export', []);
    }

    /**
     * Get chart configuration.
     */
    protected function getChartConfig(): array
    {
        return $this->getReportsConfig('charts', []);
    }

    /**
     * Get performance configuration.
     */
    protected function getPerformanceConfig(): array
    {
        return $this->getReportsConfig('performance', []);
    }

    /**
     * Check if debug mode is enabled.
     */
    protected function isDebugMode(): bool
    {
        return $this->getReportsConfig('environment.debug_mode', false);
    }

    /**
     * Log a message with reports context.
     */
    protected function logReports(string $level, string $message, array $context = []): void
    {
        $tenantId = $this->getCurrentTenantId();
        
        $context = array_merge($context, [
            'package' => 'alpha-reports',
            'tenant_id' => $tenantId,
            'saasykit_available' => $this->isSaaSykitAvailable(),
        ]);

        $logger = App::make('log');
        $logger->{$level}($message, $context);
    }

    /**
     * Track report usage if available.
     */
    protected function trackReportUsage(string $reportType, array $metadata = []): void
    {
        if ($this->isSaaSykitAvailable()) {
            $saasykitService = App::make(\Alpha\Reports\Services\SaaSykitReportsService::class);
            $saasykitService->trackReportUsage($reportType, $metadata);
        }

        // Always log usage for analytics
        $this->logReports('info', 'Report usage tracked', [
            'report_type' => $reportType,
            'metadata' => $metadata,
        ]);
    }
}