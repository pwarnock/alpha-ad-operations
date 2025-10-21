<?php

namespace Alpha\Reports\Commands;

use Illuminate\Console\Command;
use Alpha\Reports\Services\MigrationManager;
use Alpha\Reports\Services\EnvironmentDetectionService;

class ManageMigrationsCommand extends Command
{
    protected $signature = 'reports:migrate 
                            {action : The action to perform (run|rollback|status|validate)}
                            {--steps=1 : Number of migration steps to rollback}
                            {--force : Force operation in production}
                            {--detailed : Show detailed output}';

    protected $description = 'Manage Alpha Reports package migrations with environment awareness';

    public function handle(MigrationManager $migrationManager, EnvironmentDetectionService $envDetector): int
    {
        $action = $this->argument('action');
        $detailed = $this->option('detailed');

        $this->info('🚀 Alpha Reports Migration Manager');
        $this->info('==================================');

        // Show environment information
        $this->showEnvironmentInfo($envDetector);

        switch ($action) {
            case 'run':
                return $this->runMigrations($migrationManager, $detailed);
            
            case 'rollback':
                return $this->rollbackMigrations($migrationManager, $detailed);
            
            case 'status':
                return $this->showStatus($migrationManager, $detailed);
            
            case 'validate':
                return $this->validateEnvironment($migrationManager, $detailed);
            
            default:
                $this->error("Invalid action: {$action}");
                $this->info('Available actions: run, rollback, status, validate');
                return 1;
        }
    }

    protected function runMigrations(MigrationManager $migrationManager, bool $detailed): int
    {
        $this->info('📦 Running migrations...');

        if (!$this->confirmToProceed()) {
            return 0;
        }

        $result = $migrationManager->runMigrations();

        if ($result['success']) {
            $this->info('✅ Migrations completed successfully!');
            
            if (!empty($result['migrations_run'])) {
                $this->table(
                    ['Migration', 'Status', 'Execution Time'],
                    collect($result['migrations_run'])->map(function ($migration) {
                        return [
                            $migration['migration'],
                            $migration['success'] ? '✅ Success' : '❌ Failed',
                            ($migration['execution_time'] ?? 0) . 'ms',
                        ];
                    })
                );
            }

            if (isset($result['message'])) {
                $this->line($result['message']);
            }
        } else {
            $this->error('❌ Migration failed!');
            
            if (!empty($result['errors'])) {
                $this->error('Errors:');
                foreach ($result['errors'] as $error) {
                    $this->line("  - {$error['message']}");
                    if ($detailed) {
                        $this->line("    File: {$error['file']}:{$error['line']}");
                    }
                }
            }
        }

        return $result['success'] ? 0 : 1;
    }

    protected function rollbackMigrations(MigrationManager $migrationManager, bool $detailed): int
    {
        $steps = $this->option('steps');
        $this->info("🔄 Rolling back {$steps} migration(s)...");

        if (!$this->confirmToProceed()) {
            return 0;
        }

        $result = $migrationManager->rollbackMigrations($steps);

        if ($result['success']) {
            $this->info('✅ Rollback completed successfully!');
            
            if (!empty($result['migrations_rolled_back'])) {
                $this->table(
                    ['Migration', 'Status'],
                    collect($result['migrations_rolled_back'])->map(function ($migration) {
                        return [
                            $migration['migration'],
                            $migration['success'] ? '✅ Success' : '❌ Failed',
                        ];
                    })
                );
            }
        } else {
            $this->error('❌ Rollback failed!');
            
            if (!empty($result['errors'])) {
                $this->error('Errors:');
                foreach ($result['errors'] as $error) {
                    $this->line("  - {$error['message']}");
                    if ($detailed) {
                        $this->line("    File: {$error['file']}:{$error['line']}");
                    }
                }
            }
        }

        return $result['success'] ? 0 : 1;
    }

    protected function showStatus(MigrationManager $migrationManager, bool $detailed): int
    {
        $this->info('📊 Migration Status');
        $this->info('==================');

        $status = $migrationManager->getMigrationStatus();

        // Show pending migrations
        if (!empty($status['pending_migrations'])) {
            $this->info("\n🔄 Pending Migrations:");
            $this->table(
                ['Migration', 'Path'],
                collect($status['pending_migrations'])->map(function ($migration) {
                    return [
                        $migration['migration'],
                        $migration['path'],
                    ];
                })
            );
        } else {
            $this->info("\n✅ No pending migrations");
        }

        // Show ran migrations
        if (!empty($status['ran_migrations'])) {
            $this->info("\n✅ Completed Migrations:");
            $this->table(
                ['Migration', 'Batch'],
                collect($status['ran_migrations'])->map(function ($migration) {
                    return [
                        $migration['migration'],
                        $migration['batch'],
                    ];
                })
            );
        }

        // Show table status
        if ($detailed && !empty($status['table_status'])) {
            $this->info("\n📋 Table Status:");
            foreach ($status['table_status'] as $table => $info) {
                $statusIcon = $info['exists'] ? '✅' : '❌';
                $this->line("  {$statusIcon} {$table}");
                
                if ($info['exists']) {
                    $this->line("    Columns: " . implode(', ', $info['columns']));
                    $this->line("    Rows: {$info['row_count']}");
                    
                    if (isset($info['warnings'])) {
                        foreach ($info['warnings'] as $warning) {
                            $this->warn("    ⚠️  {$warning}");
                        }
                    }
                }
                
                if (isset($info['error'])) {
                    $this->error("    Error: {$info['error']}");
                }
            }
        }

        // Show recommendations
        if (!empty($status['recommendations'])) {
            $this->info("\n💡 Recommendations:");
            foreach ($status['recommendations'] as $rec) {
                $icon = $rec['type'] === 'warning' ? '⚠️' : 'ℹ️';
                $this->line("  {$icon} {$rec['message']}");
            }
        }

        return 0;
    }

    protected function validateEnvironment(MigrationManager $migrationManager, bool $detailed): int
    {
        $this->info('🔍 Environment Validation');
        $this->info('========================');

        $validation = $migrationManager->validateEnvironment();

        if ($validation['valid']) {
            $this->info('✅ Environment is valid for migrations');
        } else {
            $this->error('❌ Environment validation failed');
        }

        // Show errors
        if (!empty($validation['errors'])) {
            $this->error("\n❌ Errors:");
            foreach ($validation['errors'] as $error) {
                $this->line("  - {$error}");
            }
        }

        // Show warnings
        if (!empty($validation['warnings'])) {
            $this->warn("\n⚠️  Warnings:");
            foreach ($validation['warnings'] as $warning) {
                $this->line("  - {$warning}");
            }
        }

        // Show detailed environment info
        if ($detailed) {
            $this->info("\n🌍 Environment Details:");
            $env = $validation['environment'];
            
            $this->line("  Laravel Version: {$env['laravel_version']}");
            $this->line("  PHP Version: {$env['php_version']}");
            $this->line("  Environment: {$env['environment']}");
            $this->line("  Database: {$env['database']['driver']} ({$env['database']['connection']})");
            $this->line("  Cache: {$env['cache']['driver']}");
            $this->line("  Queue: {$env['queue']['driver']}");
            $this->line("  SaaSykit: " . ($env['saasykit']['available'] ? 'Available' : 'Not Available'));
            $this->line("  Laravel Tenancy: " . ($env['laravel_tenancy']['available'] ? 'Available' : 'Not Available'));
        }

        return $validation['valid'] ? 0 : 1;
    }

    protected function showEnvironmentInfo(EnvironmentDetectionService $envDetector): void
    {
        $env = $envDetector->detect();
        
        $this->info("🌍 Environment: {$env['environment']} (Laravel {$env['laravel_version']})");
        $this->line("💾 Database: {$env['database']['driver']}");
        $this->line("🗄️  Cache: {$env['cache']['driver']}");
        $this->line("⏰ Queue: {$env['queue']['driver']}");
        
        if ($env['saasykit']['available']) {
            $this->info("🏢 SaaSykit: Available");
        } elseif ($env['laravel_tenancy']['available']) {
            $this->info("🏢 Laravel Tenancy: Available");
        } else {
            $this->comment("🏢 Multi-tenancy: Not Available (Standalone Mode)");
        }
        
        $this->line('');
    }

    protected function confirmToProceed(): bool
    {
        if ($this->option('force')) {
            return true;
        }

        if (app()->environment('production')) {
            return $this->confirm('⚠️  You are in production mode. Do you really wish to run this command?');
        }

        return true;
    }
}