<?php

namespace Alpha\Reports\Contracts;

interface SaaSykitCompatible
{
    /**
     * Check if the current tenant can create reports.
     */
    public function canCreateReports(): bool;

    /**
     * Get the maximum number of reports allowed for the current tenant.
     */
    public function getReportLimit(): int;

    /**
     * Get available features for the current tenant.
     */
    public function getAvailableFeatures(): array;

    /**
     * Track report usage for billing/analytics.
     */
    public function trackReportUsage(string $reportType, array $metadata = []): void;

    /**
     * Get current tenant ID.
     */
    public function getCurrentTenantId(): ?int;

    /**
     * Check if a specific feature is available.
     */
    public function hasFeature(string $feature): bool;

    /**
     * Get subscription tier for current tenant.
     */
    public function getSubscriptionTier(): string;
}