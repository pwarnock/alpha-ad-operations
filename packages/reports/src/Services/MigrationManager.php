<?php

namespace Alpha\Reports\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;

class MigrationManager
{
    protected EnvironmentDetectionService $envDetector;
    protected Migrator $migrator;
    protected array $migrationPaths = [];
    protected array $environmentInfo = [];

    public function __construct(EnvironmentDetectionService $envDetector, Migrator $migrator)
    {
        $this->envDetector = $envDetector;
        $this->migrator = $migrator;
        $this->environmentInfo = $this->envDetector->detect();
        $this->setupMigrationPaths();
    }

    /**
     * Run all pending migrations for the reports package.
     */
    public function runMigrations(): array
    {
        $results = [
            'success' => false,
            'migrations_run' => [],
            'errors' => [],
            'environment' => $this->environmentInfo,
        ];

        try {
            // Get pending migrations before running
            $pendingBefore = $this->getPendingMigrations();

            if (empty($pendingBefore)) {
                $results['success'] = true;
                $results['message'] = 'No pending migrations to run';
                return $results;
            }

            $startTime = microtime(true);
            
            // Run migrations using Artisan
            $exitCode = Artisan::call('migrate', [
                '--path' => $this->migrationPaths,
                '--force' => true,
            ]);
            
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            // Get pending migrations after running
            $pendingAfter = $this->getPendingMigrations();
            $completed = array_diff($pendingBefore, $pendingAfter);

            foreach ($completed as $migration) {
                $results['migrations_run'][] = [
                    'migration' => $migration,
                    'success' => true,
                    'execution_time' => $executionTime / count($completed),
                ];
            }

            $results['success'] = $exitCode === 0 && empty($pendingAfter);

            if (!$results['success']) {
                $results['errors'][] = [
                    'message' => Artisan::output(),
                ];
            }

            Log::info("MigrationManager: Migrations completed", [
                'completed_count' => count($completed),
                'execution_time_ms' => $executionTime,
                'environment' => $this->environmentInfo,
            ]);

        } catch (\Exception $e) {
            $results['errors'][] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
            Log::error('MigrationManager: Failed to run migrations', [
                'error' => $e->getMessage(),
                'environment' => $this->environmentInfo,
            ]);
        }

        return $results;
    }

    /**
     * Rollback the last batch of migrations.
     */
    public function rollbackMigrations(int $steps = 1): array
    {
        $results = [
            'success' => false,
            'migrations_rolled_back' => [],
            'errors' => [],
        ];

        try {
            $rolledBack = $this->migrator->rollback($this->migrationPaths, $steps);
            
            foreach ($rolledBack as $migration) {
                $results['migrations_rolled_back'][] = [
                    'migration' => $migration,
                    'success' => true,
                ];
            }

            $results['success'] = true;

        } catch (\Exception $e) {
            $results['errors'][] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
            Log::error('MigrationManager: Failed to rollback migrations', [
                'error' => $e->getMessage(),
                'steps' => $steps,
            ]);
        }

        return $results;
    }

    /**
     * Get the migration status.
     */
    public function getMigrationStatus(): array
    {
        $status = [
            'environment' => $this->environmentInfo,
            'pending_migrations' => [],
            'ran_migrations' => [],
            'table_status' => [],
            'recommendations' => [],
        ];

        try {
            // Check if migration repository exists
            if (!$this->migrator->repositoryExists()) {
                $status['recommendations'][] = [
                    'type' => 'warning',
                    'message' => 'Migration repository not found. Run migrations first.',
                ];
                return $status;
            }

            // Get migration status
            $ran = $this->migrator->getRepository()->getRan();
            $pending = $this->getPendingMigrations();

            foreach ($pending as $migration) {
                $status['pending_migrations'][] = [
                    'migration' => $migration,
                    'path' => $this->getMigrationPath($migration),
                ];
            }

            foreach ($ran as $migration) {
                $status['ran_migrations'][] = [
                    'migration' => $migration,
                    'batch' => $this->getMigrationBatch($migration),
                ];
            }

            // Check table status
            $status['table_status'] = $this->checkTableStatus();

            // Generate recommendations
            $status['recommendations'] = $this->generateRecommendations();

        } catch (\Exception $e) {
            Log::error('MigrationManager: Failed to get migration status', [
                'error' => $e->getMessage(),
            ]);
            $status['errors'][] = $e->getMessage();
        }

        return $status;
    }

    /**
     * Validate migration environment and dependencies.
     */
    public function validateEnvironment(): array
    {
        $validation = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
            'environment' => $this->environmentInfo,
        ];

        // Check database support
        $dbDriver = $this->environmentInfo['database']['driver'] ?? 'unknown';
        if (!in_array($dbDriver, ['mysql', 'pgsql', 'sqlite'])) {
            $validation['errors'][] = "Unsupported database driver: {$dbDriver}";
            $validation['valid'] = false;
        }

        // Check for required features
        if (!$this->environmentInfo['database']['supports_json']) {
            $validation['errors'][] = "Database does not support JSON columns";
            $validation['valid'] = false;
        }

        // Check migration paths
        if (empty($this->migrationPaths)) {
            $validation['errors'][] = "No migration paths found";
            $validation['valid'] = false;
        }

        // Warnings for suboptimal configurations
        if ($dbDriver === 'sqlite') {
            $validation['warnings'][] = "SQLite detected. Consider MySQL/PostgreSQL for production.";
        }

        if (!$this->environmentInfo['cache']['distributed']) {
            $validation['warnings'][] = "Non-distributed cache detected. Consider Redis for multi-server deployments.";
        }

        return $validation;
    }

    /**
     * Setup migration paths based on environment.
     */
    protected function setupMigrationPaths(): void
    {
        $basePath = dirname(__DIR__, 2) . '/database/migrations';
        
        if (is_dir($basePath)) {
            $this->migrationPaths[] = $basePath;
        }

        // Add environment-specific migration paths
        if ($this->envDetector->isSaaSykitAvailable()) {
            $saasykitPath = $basePath . '/saasykit';
            if (is_dir($saasykitPath)) {
                $this->migrationPaths[] = $saasykitPath;
            }
        } elseif ($this->envDetector->isLaravelTenancyAvailable()) {
            $tenancyPath = $basePath . '/tenancy';
            if (is_dir($tenancyPath)) {
                $this->migrationPaths[] = $tenancyPath;
            }
        }
    }

    /**
     * Ensure migration repository exists.
     */
    protected function ensureMigrationRepository(): void
    {
        if (!$this->migrator->repositoryExists()) {
            $repository = new DatabaseMigrationRepository(
                $this->migrator->getDatabaseConnection(),
                $this->migrator->getRepositoryTable()
            );
            $repository->createRepository();
        }
    }

    /**
     * Get pending migrations.
     */
    protected function getPendingMigrations(): array
    {
        $files = $this->migrator->getMigrationFiles($this->migrationPaths);
        return array_diff(array_keys($files), $this->migrator->getRepository()->getRan());
    }

    /**
     * Run a single migration.
     */
    protected function runMigration(string $migration): array
    {
        $result = [
            'migration' => $migration,
            'success' => false,
            'error' => null,
            'execution_time' => 0,
        ];

        try {
            $startTime = microtime(true);
            
            // Run the migration using Artisan command
            $exitCode = Artisan::call('migrate', [
                '--path' => $this->getMigrationPath($migration),
                '--force' => true,
            ]);
            
            $result['success'] = $exitCode === 0;
            $result['execution_time'] = round((microtime(true) - $startTime) * 1000, 2);
            
            if ($result['success']) {
                Log::info("MigrationManager: Migration completed", [
                    'migration' => $migration,
                    'execution_time_ms' => $result['execution_time'],
                    'environment' => $this->environmentInfo,
                ]);
            } else {
                $result['error'] = [
                    'message' => Artisan::output(),
                ];
            }

        } catch (\Exception $e) {
            $result['error'] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
            
            Log::error("MigrationManager: Migration failed", [
                'migration' => $migration,
                'error' => $e->getMessage(),
                'environment' => $this->environmentInfo,
            ]);
        }

        return $result;
    }

    /**
     * Get migration file path.
     */
    protected function getMigrationPath(string $migration): string
    {
        foreach ($this->migrationPaths as $path) {
            $filePath = $path . '/' . $migration . '.php';
            if (file_exists($filePath)) {
                return $filePath;
            }
        }
        
        throw new \RuntimeException("Migration file not found: {$migration}");
    }

    /**
     * Check table status for reports tables.
     */
    protected function checkTableStatus(): array
    {
        $tables = [
            'alpha_reports',
            'alpha_report_schedules',
            'alpha_report_executions',
            'alpha_report_shares',
            'alpha_report_access_logs',
            'alpha_report_comments',
        ];

        $status = [];

        foreach ($tables as $table) {
            $status[$table] = [
                'exists' => Schema::hasTable($table),
                'columns' => [],
                'indexes' => [],
                'row_count' => 0,
            ];

            if ($status[$table]['exists']) {
                try {
                    // Get column information
                    $columns = Schema::getColumnListing($table);
                    $status[$table]['columns'] = $columns;

                    // Get row count
                    $status[$table]['row_count'] = DB::table($table)->count();

                    // Check for tenant columns if multi-tenant
                    if ($this->envDetector->hasFeature('tenant_isolation')) {
                        $hasTenantColumns = in_array('tenant_id', $columns);
                        $status[$table]['has_tenant_columns'] = $hasTenantColumns;
                        
                        if (!$hasTenantColumns) {
                            $status[$table]['warnings'][] = 'Missing tenant_id column in multi-tenant environment';
                        }
                    }

                } catch (\Exception $e) {
                    $status[$table]['error'] = $e->getMessage();
                }
            }
        }

        return $status;
    }

    /**
     * Generate recommendations based on current state.
     */
    protected function generateRecommendations(): array
    {
        $recommendations = [];

        // Check for pending migrations
        $pending = $this->getPendingMigrations();
        if (!empty($pending)) {
            $recommendations[] = [
                'type' => 'info',
                'message' => count($pending) . ' pending migrations found. Run migrations to update database.',
                'action' => 'run_migrations',
            ];
        }

        // Environment-specific recommendations
        if (!$this->envDetector->isSaaSykitAvailable() && !$this->envDetector->isLaravelTenancyAvailable()) {
            $recommendations[] = [
                'type' => 'info',
                'message' => 'Running in single-tenant mode. Consider SaaSykit or Laravel Tenancy for multi-tenant support.',
            ];
        }

        // Database recommendations
        $dbDriver = $this->environmentInfo['database']['driver'] ?? 'unknown';
        if ($dbDriver === 'sqlite') {
            $recommendations[] = [
                'type' => 'warning',
                'message' => 'SQLite is not recommended for production. Consider MySQL or PostgreSQL.',
            ];
        }

        return $recommendations;
    }

    /**
     * Get migration batch number.
     */
    protected function getMigrationBatch(string $migration): ?int
    {
        try {
            $record = DB::table('migrations')
                ->where('migration', $migration)
                ->first();
            return $record ? $record->batch : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}