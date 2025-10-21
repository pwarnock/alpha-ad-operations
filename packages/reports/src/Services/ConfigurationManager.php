<?php

namespace Alpha\Reports\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class ConfigurationManager
{
    protected EnvironmentDetectionService $environmentDetection;
    protected array $runtimeConfig = [];
    protected array $featureOverrides = [];

    public function __construct(EnvironmentDetectionService $environmentDetection)
    {
        $this->environmentDetection = $environmentDetection;
        $this->initializeConfiguration();
    }

    /**
     * Initialize configuration based on environment detection.
     */
    protected function initializeConfiguration(): void
    {
        // Detect environment
        $this->environmentDetection->detect();

        // Apply environment-specific configuration
        $this->applyEnvironmentConfiguration();

        // Apply feature-based configuration
        $this->applyFeatureConfiguration();

        // Apply performance optimizations
        $this->applyPerformanceOptimizations();

        Log::info('Reports configuration initialized', [
            'environment' => $this->environmentDetection->getEnvironmentInfo(),
            'runtime_config' => $this->runtimeConfig,
        ]);
    }

    /**
     * Apply environment-specific configuration.
     */
    protected function applyEnvironmentConfiguration(): void
    {
        $envInfo = $this->environmentDetection->getEnvironmentInfo();

        // SaaSykit-specific configuration
        if ($this->environmentDetection->isSaaSykitAvailable()) {
            $this->setRuntimeConfig('saasykit.integration_mode', 'full');
            $this->setRuntimeConfig('tenant.isolation_mode', 'strict');
            $this->setRuntimeConfig('features.subscription_limits', true);
            $this->setRuntimeConfig('features.usage_tracking', true);
        } elseif ($this->environmentDetection->isLaravelTenancyAvailable()) {
            $this->setRuntimeConfig('saasykit.integration_mode', 'tenancy_only');
            $this->setRuntimeConfig('tenant.isolation_mode', 'strict');
            $this->setRuntimeConfig('features.subscription_limits', false);
            $this->setRuntimeConfig('features.usage_tracking', false);
        } else {
            $this->setRuntimeConfig('saasykit.integration_mode', 'standalone');
            $this->setRuntimeConfig('tenant.isolation_mode', 'disabled');
            $this->setRuntimeConfig('features.subscription_limits', false);
            $this->setRuntimeConfig('features.usage_tracking', false);
        }

        // Database-specific configuration
        $dbDriver = $envInfo['database']['driver'] ?? 'mysql';
        switch ($dbDriver) {
            case 'sqlite':
                $this->setRuntimeConfig('performance.query.max_rows', 10000);
                $this->setRuntimeConfig('performance.query.chunk_size', 500);
                $this->setRuntimeConfig('performance.query.timeout', 15);
                break;
            case 'mysql':
                $this->setRuntimeConfig('performance.query.max_rows', 100000);
                $this->setRuntimeConfig('performance.query.chunk_size', 1000);
                $this->setRuntimeConfig('performance.query.timeout', 30);
                break;
            case 'pgsql':
                $this->setRuntimeConfig('performance.query.max_rows', 100000);
                $this->setRuntimeConfig('performance.query.chunk_size', 1000);
                $this->setRuntimeConfig('performance.query.timeout', 30);
                break;
        }

        // Cache-specific configuration
        $cacheDriver = $envInfo['cache']['driver'] ?? 'file';
        if ($cacheDriver === 'redis') {
            $this->setRuntimeConfig('performance.cache.default_ttl', 3600);
            $this->setRuntimeConfig('features.real_time_updates', true);
        } else {
            $this->setRuntimeConfig('performance.cache.default_ttl', 1800);
            $this->setRuntimeConfig('features.real_time_updates', false);
        }

        // Queue-specific configuration
        $queueDriver = $envInfo['queue']['driver'] ?? 'sync';
        if ($queueDriver === 'sync') {
            $this->setRuntimeConfig('features.scheduled_reports', false);
            $this->setRuntimeConfig('performance.background.concurrent_jobs', 1);
        } else {
            $this->setRuntimeConfig('features.scheduled_reports', true);
            $this->setRuntimeConfig('performance.background.concurrent_jobs', 3);
        }
    }

    /**
     * Apply feature-based configuration.
     */
    protected function applyFeatureConfiguration(): void
    {
        $detectedFeatures = $this->environmentDetection->getDetectedFeatures();

        foreach ($detectedFeatures as $feature => $available) {
            // Only enable features that are both configured and detected as available
            $configEnabled = Config::get("reports.features.{$feature}", false);
            $this->setRuntimeConfig("features.{$feature}", $configEnabled && $available);

            if (!$available && $configEnabled) {
                Log::warning("Feature '{$feature}' is enabled in config but not available in current environment", [
                    'feature' => $feature,
                    'config_enabled' => $configEnabled,
                    'detected_available' => $available,
                ]);
            }
        }

        // Apply feature dependencies
        $this->applyFeatureDependencies();
    }

    /**
     * Apply feature dependencies and constraints.
     */
    protected function applyFeatureDependencies(): void
    {
        // Chart visualization requires advanced filters
        if ($this->getRuntimeConfig('features.chart_visualization', false)) {
            $this->setRuntimeConfig('features.advanced_filters', true);
        }

        // Real-time updates require distributed cache
        if ($this->getRuntimeConfig('features.real_time_updates', false)) {
            $cacheDriver = $this->environmentDetection->getEnvironmentInfo()['cache']['driver'] ?? 'file';
            if (!in_array($cacheDriver, ['redis', 'memcached'])) {
                $this->setRuntimeConfig('features.real_time_updates', false);
                Log::warning('Real-time updates disabled: requires distributed cache (Redis/Memcached)');
            }
        }

        // API access requires proper authentication
        if ($this->getRuntimeConfig('features.api_access', false)) {
            if (!Config::get('reports.api.auth_required', true)) {
                Log::warning('API access enabled without authentication - this is not recommended for production');
            }
        }

        // Export format dependencies
        $exportFormats = $this->getRuntimeConfig('export.formats.enabled', []);
        if (in_array('excel', $exportFormats) && !extension_loaded('zip')) {
            $exportFormats = array_diff($exportFormats, ['excel']);
            $this->setRuntimeConfig('export.formats.enabled', $exportFormats);
            Log::warning('Excel export disabled: ZIP extension not available');
        }

        if (in_array('pdf', $exportFormats) && !class_exists('Dompdf\Dompdf')) {
            $exportFormats = array_diff($exportFormats, ['pdf']);
            $this->setRuntimeConfig('export.formats.enabled', $exportFormats);
            Log::warning('PDF export disabled: DOMPDF library not available');
        }
    }

    /**
     * Apply performance optimizations based on environment.
     */
    protected function applyPerformanceOptimizations(): void
    {
        $envInfo = $this->environmentDetection->getEnvironmentInfo();

        // Memory optimization for production
        if ($envInfo['environment'] === 'production') {
            $this->setRuntimeConfig('performance.memory.gc_collection', true);
            $this->setRuntimeConfig('performance.memory.optimize_exports', true);
        }

        // Query optimization for large datasets
        if ($envInfo['database']['supports_full_text'] ?? false) {
            $this->setRuntimeConfig('performance.query.optimize_large_queries', true);
        }

        // Cache optimization for distributed environments
        if ($envInfo['cache']['distributed'] ?? false) {
            $this->setRuntimeConfig('performance.cache.tags', ['alpha-reports', 'distributed']);
        }

        // Background processing optimization
        if ($envInfo['queue']['supports_retries'] ?? false) {
            $this->setRuntimeConfig('performance.background.max_attempts', 3);
            $this->setRuntimeConfig('performance.background.retry_delay', 60);
        }
    }

    /**
     * Get configuration value with runtime overrides.
     */
    public function get(string $key, $default = null)
    {
        // Check runtime config first
        if (Arr::has($this->runtimeConfig, $key)) {
            return Arr::get($this->runtimeConfig, $key, $default);
        }

        // Check feature overrides
        if (Arr::has($this->featureOverrides, $key)) {
            return Arr::get($this->featureOverrides, $key, $default);
        }

        // Fall back to Laravel config
        return Config::get("reports.{$key}", $default);
    }

    /**
     * Set runtime configuration value.
     */
    public function setRuntimeConfig(string $key, $value): void
    {
        Arr::set($this->runtimeConfig, $key, $value);
    }

    /**
     * Set feature override.
     */
    public function setFeatureOverride(string $feature, bool $enabled): void
    {
        $this->featureOverrides[$feature] = $enabled;
        $this->setRuntimeConfig("features.{$feature}", $enabled);
    }

    /**
     * Get all runtime configuration.
     */
    public function getRuntimeConfig(): array
    {
        return $this->runtimeConfig;
    }

    /**
     * Get all feature overrides.
     */
    public function getFeatureOverrides(): array
    {
        return $this->featureOverrides;
    }

    /**
     * Check if a feature is enabled.
     */
    public function isFeatureEnabled(string $feature): bool
    {
        return $this->get("features.{$feature}", false);
    }

    /**
     * Get effective configuration for a specific area.
     */
    public function getEffectiveConfig(string $area): array
    {
        $baseConfig = Config::get("reports.{$area}", []);
        $runtimeOverrides = Arr::get($this->runtimeConfig, $area, []);

        return array_replace_recursive($baseConfig, $runtimeOverrides);
    }

    /**
     * Get tenant-aware configuration.
     */
    public function getTenantConfig(?int $tenantId = null): array
    {
        $config = $this->getEffectiveConfig('tenant');
        
        if ($tenantId && $this->environmentDetection->isSaaSykitAvailable()) {
            // Apply tenant-specific overrides if available
            $tenantOverrides = $this->getTenantSpecificOverrides($tenantId);
            $config = array_replace_recursive($config, $tenantOverrides);
        }

        return $config;
    }

    /**
     * Get tenant-specific configuration overrides.
     */
    protected function getTenantSpecificOverrides(int $tenantId): array
    {
        // This could be extended to load tenant-specific config from database
        // For now, return empty array
        return [];
    }

    /**
     * Validate configuration and return any issues.
     */
    public function validateConfiguration(): array
    {
        $issues = [];

        // Check for required features
        if ($this->isFeatureEnabled('basic_reports') && !$this->environmentDetection->hasFeature('basic_reports')) {
            $issues[] = [
                'type' => 'error',
                'message' => 'Basic reports feature is enabled but not available in current environment',
                'config' => 'features.basic_reports',
            ];
        }

        // Check for incompatible combinations
        if ($this->isFeatureEnabled('real_time_updates') && !$this->environmentDetection->getEnvironmentInfo()['cache']['distributed']) {
            $issues[] = [
                'type' => 'warning',
                'message' => 'Real-time updates enabled but distributed cache not available',
                'config' => 'features.real_time_updates',
            ];
        }

        // Check for security issues
        if ($this->isFeatureEnabled('api_access') && !$this->get('api.auth_required', true)) {
            $issues[] = [
                'type' => 'security',
                'message' => 'API access enabled without authentication',
                'config' => 'api.auth_required',
            ];
        }

        return $issues;
    }

    /**
     * Get configuration recommendations.
     */
    public function getRecommendations(): array
    {
        $recommendations = $this->environmentDetection->getRecommendedConfiguration();
        $validationIssues = $this->validateConfiguration();

        return array_merge($recommendations, $validationIssues);
    }

    /**
     * Refresh configuration (re-detect environment and reapply config).
     */
    public function refresh(): void
    {
        $this->environmentDetection->refresh();
        $this->runtimeConfig = [];
        $this->featureOverrides = [];
        $this->initializeConfiguration();
    }

    /**
     * Export current configuration for debugging.
     */
    public function exportConfiguration(): array
    {
        return [
            'environment' => $this->environmentDetection->getEnvironmentInfo(),
            'base_config' => Config::get('reports'),
            'runtime_config' => $this->runtimeConfig,
            'feature_overrides' => $this->featureOverrides,
            'effective_config' => [
                'features' => $this->getEffectiveConfig('features'),
                'performance' => $this->getEffectiveConfig('performance'),
                'tenant' => $this->getEffectiveConfig('tenant'),
                'export' => $this->getEffectiveConfig('export'),
            ],
            'validation_issues' => $this->validateConfiguration(),
            'recommendations' => $this->getRecommendations(),
        ];
    }
}