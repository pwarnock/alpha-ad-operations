<?php

namespace Alpha\Reports\Commands;

use Illuminate\Console\Command;
use Alpha\Reports\Services\EnvironmentDetectionService;
use Alpha\Reports\Services\ConfigurationManager;

class ValidateConfigurationCommand extends Command
{
    protected $signature = 'reports:validate-config {--detailed : Show detailed configuration information}';
    protected $description = 'Validate Alpha Reports configuration and show environment information';

    public function handle(EnvironmentDetectionService $envDetection, ConfigurationManager $configManager): int
    {
        $this->info('🔍 Validating Alpha Reports Configuration...');
        $this->newLine();

        // Show environment information
        $this->showEnvironmentInfo($envDetection);

        // Show detected features
        $this->showDetectedFeatures($envDetection);

        // Show configuration validation
        $this->showConfigurationValidation($configManager);

        // Show recommendations
        $this->showRecommendations($configManager);

        // Show detailed configuration if requested
        if ($this->option('detailed')) {
            $this->showDetailedConfiguration($configManager);
        }

        $this->newLine();
        $this->info('✅ Configuration validation complete!');

        return Command::SUCCESS;
    }

    protected function showEnvironmentInfo(EnvironmentDetectionService $envDetection): void
    {
        $this->info('📊 Environment Information:');
        $envInfo = $envDetection->getEnvironmentInfo();

        $this->table(
            ['Setting', 'Value'],
            [
                ['Laravel Version', $envInfo['laravel_version']],
                ['PHP Version', $envInfo['php_version']],
                ['Environment', $envInfo['environment']],
                ['Debug Mode', $envInfo['debug_mode'] ? 'Yes' : 'No'],
                ['Database', $envInfo['database']['driver']],
                ['Cache Driver', $envInfo['cache']['driver']],
                ['Queue Driver', $envInfo['queue']['driver']],
                ['SaaSykit Available', $envInfo['saasykit']['available'] ? 'Yes' : 'No'],
                ['Laravel Tenancy Available', $envInfo['laravel_tenancy']['available'] ? 'Yes' : 'No'],
            ]
        );

        $this->newLine();
    }

    protected function showDetectedFeatures(EnvironmentDetectionService $envDetection): void
    {
        $this->info('🎯 Detected Features:');
        $features = $envDetection->getDetectedFeatures();

        $rows = [];
        foreach ($features as $feature => $available) {
            $status = $available ? '✅ Available' : '❌ Not Available';
            $rows[] = [ucwords(str_replace('_', ' ', $feature)), $status];
        }

        $this->table(['Feature', 'Status'], $rows);
        $this->newLine();
    }

    protected function showConfigurationValidation(ConfigurationManager $configManager): void
    {
        $this->info('⚙️ Configuration Validation:');
        $issues = $configManager->validateConfiguration();

        if (empty($issues)) {
            $this->info('✅ No configuration issues found!');
        } else {
            foreach ($issues as $issue) {
                $icon = match($issue['type']) {
                    'error' => '❌',
                    'warning' => '⚠️',
                    'security' => '🔒',
                    default => 'ℹ️',
                };

                $this->line("{$icon} {$issue['message']}");
                if (isset($issue['config'])) {
                    $this->line("   Config: {$issue['config']}");
                }
            }
        }

        $this->newLine();
    }

    protected function showRecommendations(ConfigurationManager $configManager): void
    {
        $this->info('💡 Recommendations:');
        $recommendations = $configManager->getRecommendations();

        if (empty($recommendations)) {
            $this->info('✅ No recommendations - your configuration looks good!');
        } else {
            foreach ($recommendations as $rec) {
                $icon = match($rec['type']) {
                    'warning' => '⚠️',
                    'info' => 'ℹ️',
                    'error' => '❌',
                    'security' => '🔒',
                    default => '💡',
                };

                $this->line("{$icon} {$rec['message']}");
                
                if (isset($rec['config'])) {
                    $this->line("   Suggested config changes:");
                    foreach ($rec['config'] as $key => $value) {
                        $this->line("     - {$key}: " . (is_bool($value) ? ($value ? 'true' : 'false') : $value));
                    }
                }
            }
        }

        $this->newLine();
    }

    protected function showDetailedConfiguration(ConfigurationManager $configManager): void
    {
        $this->info('📋 Detailed Configuration:');
        $config = $configManager->exportConfiguration();

        // Show effective features
        $this->info('Effective Features:');
        $features = $config['effective_config']['features'];
        $rows = [];
        foreach ($features as $feature => $enabled) {
            $rows[] = [ucwords(str_replace('_', ' ', $feature)), $enabled ? 'Enabled' : 'Disabled'];
        }
        $this->table(['Feature', 'Status'], $rows);
        $this->newLine();

        // Show performance settings
        $this->info('Performance Settings:');
        $perf = $config['effective_config']['performance'];
        $this->table(
            ['Setting', 'Value'],
            [
                ['Cache TTL', $perf['cache']['default_ttl'] . ' seconds'],
                ['Query Timeout', $perf['query']['timeout'] . ' seconds'],
                ['Max Query Rows', number_format($perf['query']['max_rows'])],
                ['Chunk Size', $perf['query']['chunk_size']],
                ['Concurrent Jobs', $perf['background']['concurrent_jobs']],
                ['Memory Limit', $perf['memory']['limit']],
            ]
        );
        $this->newLine();

        // Show tenant settings
        $this->info('Tenant Settings:');
        $tenant = $config['effective_config']['tenant'];
        $this->table(
            ['Setting', 'Value'],
            [
                ['Max Reports per Tenant', $tenant['max_reports_per_tenant']],
                ['Isolation Mode', $tenant['isolation_mode']],
                ['Enforce Limits', $tenant['enforce_limits'] ? 'Yes' : 'No'],
                ['Tenant-Aware Cache', $tenant['tenant_aware_cache'] ? 'Yes' : 'No'],
            ]
        );
        $this->newLine();

        // Show export settings
        $this->info('Export Settings:');
        $export = $config['effective_config']['export'];
        $formats = implode(', ', $export['formats']['enabled']);
        $this->table(
            ['Setting', 'Value'],
            [
                ['Available Formats', $formats],
                ['Default Format', $export['formats']['default']],
                ['Max File Size', $export['limits']['max_file_size']],
                ['Storage Disk', $export['storage']['disk']],
                ['Retention Days', $export['storage']['retention_days']],
            ]
        );
        $this->newLine();
    }
}