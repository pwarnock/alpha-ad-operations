<?php

namespace Alpha\Reports\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class EnvironmentDetectionService
{
    protected ?bool $saasykitDetected = null;
    protected ?bool $laravelTenancyDetected = null;
    protected array $detectedFeatures = [];
    protected array $environmentInfo = [];

    /**
     * Detect the current environment and available integrations.
     */
    public function detect(): array
    {
        $this->isSaaSykitAvailable();
        $this->isLaravelTenancyAvailable();
        $this->detectDatabaseType();
        $this->detectCacheDriver();
        $this->detectQueueDriver();
        $this->detectAvailableFeatures();
        $this->buildEnvironmentInfo();

        return $this->environmentInfo;
    }

    /**
     * Check if SaaSykit is available and properly configured.
     */
    public function isSaaSykitAvailable(): bool
    {
        if ($this->saasykitDetected !== null) {
            return $this->saasykitDetected;
        }

        $this->saasykitDetected = $this->performSaaSykitDetection();
        return $this->saasykitDetected;
    }

    /**
     * Perform the actual SaaSykit detection.
     */
    protected function performSaaSykitDetection(): bool
    {
        // Check if auto-detection is disabled
        if (!config('reports.environment.auto_detect_saasykit', true)) {
            return false;
        }

        // Check if standalone mode is forced
        if (config('reports.environment.standalone_mode', false)) {
            return false;
        }

        $app = App::getFacadeRoot();

        // Check for SaaSykit Tenant Manager
        $hasTenantManager = class_exists('\SaaSykit\Tenant\TenantManager') 
            && $app->bound(\SaaSykit\Tenant\TenantManager::class);

        // Check for SaaSykit Subscription Manager
        $hasSubscriptionManager = class_exists('\SaaSykit\Subscription\SubscriptionManager') 
            && $app->bound(\SaaSykit\Subscription\SubscriptionManager::class);

        // Check for SaaSykit Event System
        $hasEventSystem = class_exists('\SaaSykit\Events\UsageTracked');

        $isAvailable = $hasTenantManager && $hasSubscriptionManager;

        if ($isAvailable) {
            Log::info('SaaSykit detected and available', [
                'tenant_manager' => $hasTenantManager,
                'subscription_manager' => $hasSubscriptionManager,
                'event_system' => $hasEventSystem,
            ]);
        } else {
            Log::info('SaaSykit not available, running in standalone mode', [
                'tenant_manager' => $hasTenantManager,
                'subscription_manager' => $hasSubscriptionManager,
                'event_system' => $hasEventSystem,
            ]);
        }

        return $isAvailable;
    }

    /**
     * Check if Laravel Tenancy is available (alternative to SaaSykit).
     */
    public function isLaravelTenancyAvailable(): bool
    {
        if ($this->laravelTenancyDetected !== null) {
            return $this->laravelTenancyDetected;
        }

        $this->laravelTenancyDetected = $this->performLaravelTenancyDetection();
        return $this->laravelTenancyDetected;
    }

    /**
     * Perform Laravel Tenancy detection.
     */
    protected function performLaravelTenancyDetection(): bool
    {
        $app = App::getFacadeRoot();

        // Check for common Laravel Tenancy packages
        $tenancyPackages = [
            'Stancl\Tenancy\Tenancy',
            'Spatie\Multitenancy\Models\Tenant',
            'Hyn\Tenancy\Environment',
        ];

        foreach ($tenancyPackages as $package) {
            if (class_exists($package) && $app->bound($package)) {
                Log::info("Laravel Tenancy package detected: {$package}");
                return true;
            }
        }

        return false;
    }

    /**
     * Detect database type and configuration.
     */
    protected function detectDatabaseType(): void
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver", 'unknown');

        $this->environmentInfo['database'] = [
            'connection' => $connection,
            'driver' => $driver,
            'supports_json' => in_array($driver, ['mysql', 'pgsql', 'sqlite']),
            'supports_full_text' => in_array($driver, ['mysql', 'pgsql']),
            'read_replica_available' => !empty(config("database.connections.{$connection}.read")),
        ];
    }

    /**
     * Detect cache driver and capabilities.
     */
    protected function detectCacheDriver(): void
    {
        $driver = config('cache.default');
        
        $this->environmentInfo['cache'] = [
            'driver' => $driver,
            'supports_tags' => in_array($driver, ['redis', 'memcached', 'array']),
            'supports_locking' => $driver === 'redis',
            'distributed' => in_array($driver, ['redis', 'memcached', 'dynamodb']),
        ];
    }

    /**
     * Detect queue driver and capabilities.
     */
    protected function detectQueueDriver(): void
    {
        $default = config('queue.default');
        $connection = config("queue.connections.{$default}");

        $this->environmentInfo['queue'] = [
            'driver' => $default,
            'supports_delay' => isset($connection['delay']),
            'supports_retries' => isset($connection['retry_after']),
            'supports_failed_jobs' => config('queue.failed.driver') !== null,
        ];
    }

    /**
     * Detect available features based on environment.
     */
    protected function detectAvailableFeatures(): void
    {
        $features = [];

        // Basic features always available
        $features['basic_reports'] = true;
        $features['csv_export'] = true;

        // Features requiring specific capabilities
        $features['excel_export'] = extension_loaded('zip') && class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet');
        $features['pdf_export'] = class_exists('Dompdf\Dompdf') || class_exists('TCPDF');
        $features['chart_visualization'] = config('reports.charts.library') !== null;
        $features['api_access'] = config('reports.api.enabled', false);
        $features['real_time_updates'] = $this->environmentInfo['cache']['driver'] === 'redis';
        $features['scheduled_reports'] = $this->environmentInfo['queue']['driver'] !== 'sync';
        $features['advanced_filters'] = $this->environmentInfo['database']['supports_json'];
        $features['data_drilldown'] = $this->environmentInfo['database']['supports_full_text'];

        // SaaSykit-specific features
        if ($this->isSaaSykitAvailable()) {
            $features['tenant_isolation'] = true;
            $features['subscription_limits'] = true;
            $features['usage_tracking'] = true;
            $features['feature_gates'] = true;
        } elseif ($this->isLaravelTenancyAvailable()) {
            $features['tenant_isolation'] = true;
            $features['subscription_limits'] = false;
            $features['usage_tracking'] = false;
            $features['feature_gates'] = false;
        } else {
            $features['tenant_isolation'] = false;
            $features['subscription_limits'] = false;
            $features['usage_tracking'] = false;
            $features['feature_gates'] = false;
        }

        $this->detectedFeatures = $features;
        $this->environmentInfo['features'] = $features;
    }

    /**
     * Build comprehensive environment information.
     */
    protected function buildEnvironmentInfo(): void
    {
        $this->environmentInfo = array_merge($this->environmentInfo, [
            'laravel_version' => App::version(),
            'php_version' => PHP_VERSION,
            'environment' => App::environment(),
            'debug_mode' => config('app.debug', false),
            'reports_config' => [
                'saasykit_enabled' => config('reports.saasykit.enabled', true),
                'auto_detect' => config('reports.environment.auto_detect_saasykit', true),
                'standalone_mode' => config('reports.environment.standalone_mode', false),
            ],
            'saasykit' => [
                'available' => $this->isSaaSykitAvailable(),
                'tenant_manager' => class_exists('\SaaSykit\Tenant\TenantManager'),
                'subscription_manager' => class_exists('\SaaSykit\Subscription\SubscriptionManager'),
                'event_system' => class_exists('\SaaSykit\Events\UsageTracked'),
            ],
            'laravel_tenancy' => [
                'available' => $this->isLaravelTenancyAvailable(),
            ],
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get detected features.
     */
    public function getDetectedFeatures(): array
    {
        if (empty($this->detectedFeatures)) {
            $this->detect();
        }

        return $this->detectedFeatures;
    }

    /**
     * Check if a specific feature is available.
     */
    public function hasFeature(string $feature): bool
    {
        $features = $this->getDetectedFeatures();
        return $features[$feature] ?? false;
    }

    /**
     * Get environment information.
     */
    public function getEnvironmentInfo(): array
    {
        if (empty($this->environmentInfo)) {
            $this->detect();
        }

        return $this->environmentInfo;
    }

    /**
     * Get recommended configuration based on environment.
     */
    public function getRecommendedConfiguration(): array
    {
        $this->detect();
        
        $recommendations = [];

        // Database recommendations
        if ($this->environmentInfo['database']['driver'] === 'sqlite') {
            $recommendations[] = [
                'type' => 'warning',
                'message' => 'SQLite detected. Consider using MySQL or PostgreSQL for better performance with large datasets.',
                'config' => ['performance.query.max_rows' => 10000],
            ];
        }

        // Cache recommendations
        if (!$this->environmentInfo['cache']['distributed']) {
            $recommendations[] = [
                'type' => 'info',
                'message' => 'Consider using Redis for better caching performance in multi-server environments.',
                'config' => ['performance.cache.default_ttl' => 1800],
            ];
        }

        // Queue recommendations
        if ($this->environmentInfo['queue']['driver'] === 'sync') {
            $recommendations[] = [
                'type' => 'warning',
                'message' => 'Sync queue detected. Background report generation will block requests.',
                'config' => ['features.scheduled_reports' => false],
            ];
        }

        // SaaSykit recommendations
        if (!$this->isSaaSykitAvailable() && !$this->isLaravelTenancyAvailable()) {
            $recommendations[] = [
                'type' => 'info',
                'message' => 'No multi-tenancy framework detected. Running in single-tenant mode.',
                'config' => ['tenant.isolation_mode' => 'disabled'],
            ];
        }

        return $recommendations;
    }

    /**
     * Clear detection cache and re-detect.
     */
    public function refresh(): void
    {
        $this->saasykitDetected = null;
        $this->laravelTenancyDetected = null;
        $this->detectedFeatures = [];
        $this->environmentInfo = [];
        
        $this->detect();
    }

    /**
     * Get a summary of the environment for logging/debugging.
     */
    public function getSummary(): string
    {
        $this->detect();
        
        $summary = "Environment: {$this->environmentInfo['environment']} (Laravel {$this->environmentInfo['laravel_version']})\n";
        $summary .= "Database: {$this->environmentInfo['database']['driver']}\n";
        $summary .= "Cache: {$this->environmentInfo['cache']['driver']}\n";
        $summary .= "Queue: {$this->environmentInfo['queue']['driver']}\n";
        $summary .= "SaaSykit: " . ($this->isSaaSykitAvailable() ? 'Available' : 'Not Available') . "\n";
        $summary .= "Laravel Tenancy: " . ($this->isLaravelTenancyAvailable() ? 'Available' : 'Not Available') . "\n";
        
        return $summary;
    }
}