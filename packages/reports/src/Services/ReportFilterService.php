<?php

namespace Alpha\Reports\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ReportFilterService
{
    protected array $availableFilters = [
        'advertiser_id',
        'campaign_id',
        'line_item_id',
        'geography',
        'device_type',
        'date_from',
        'date_to',
    ];

    /**
     * Apply filters to a query builder.
     */
    public function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $filter => $value) {
            if ($this->isValidFilter($filter) && $this->hasValue($value)) {
                $method = 'apply' . ucfirst($filter) . 'Filter';
                if (method_exists($this, $method)) {
                    $this->$method($query, $value);
                } else {
                    $this->applyGenericFilter($query, $filter, $value);
                }
            }
        }

        return $query;
    }

    /**
     * Get available filter options for UI.
     */
    public function getFilterOptions(): array
    {
        return [
            'advertiser_id' => [
                'type' => 'select',
                'label' => 'Advertiser',
                'options' => $this->getAdvertiserOptions(),
                'multiple' => true,
            ],
            'campaign_id' => [
                'type' => 'select',
                'label' => 'Campaign',
                'options' => $this->getCampaignOptions(),
                'multiple' => true,
            ],
            'line_item_id' => [
                'type' => 'select',
                'label' => 'Line Item',
                'options' => $this->getLineItemOptions(),
                'multiple' => true,
            ],
            'geography' => [
                'type' => 'select',
                'label' => 'Geography',
                'options' => $this->getGeographyOptions(),
                'multiple' => true,
            ],
            'device_type' => [
                'type' => 'select',
                'label' => 'Device Type',
                'options' => $this->getDeviceOptions(),
                'multiple' => true,
            ],
            'date_from' => [
                'type' => 'date',
                'label' => 'Date From',
            ],
            'date_to' => [
                'type' => 'date',
                'label' => 'Date To',
            ],
        ];
    }

    /**
     * Check if filter is valid.
     */
    protected function isValidFilter(string $filter): bool
    {
        return in_array($filter, $this->availableFilters);
    }

    /**
     * Check if filter value has content.
     */
    protected function hasValue($value): bool
    {
        if (is_array($value)) {
            return !empty($value);
        }
        return !is_null($value) && $value !== '';
    }

    /**
     * Apply advertiser filter.
     */
    protected function applyAdvertiserIdFilter(Builder $query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('advertiser_id', $value);
        } else {
            $query->where('advertiser_id', $value);
        }
    }

    /**
     * Apply campaign filter.
     */
    protected function applyCampaignIdFilter(Builder $query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('campaign_id', $value);
        } else {
            $query->where('campaign_id', $value);
        }
    }

    /**
     * Apply line item filter.
     */
    protected function applyLineItemIdFilter(Builder $query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('line_item_id', $value);
        } else {
            $query->where('line_item_id', $value);
        }
    }

    /**
     * Apply geography filter.
     */
    protected function applyGeographyFilter(Builder $query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('geography', $value);
        } else {
            $query->where('geography', $value);
        }
    }

    /**
     * Apply device type filter.
     */
    protected function applyDeviceTypeFilter(Builder $query, $value): void
    {
        if (is_array($value)) {
            $query->whereIn('device_type', $value);
        } else {
            $query->where('device_type', $value);
        }
    }

    /**
     * Apply date from filter.
     */
    protected function applyDateFromFilter(Builder $query, $value): void
    {
        $query->whereDate('date', '>=', $value);
    }

    /**
     * Apply date to filter.
     */
    protected function applyDateToFilter(Builder $query, $value): void
    {
        $query->whereDate('date', '<=', $value);
    }

    /**
     * Apply generic filter.
     */
    protected function applyGenericFilter(Builder $query, string $filter, $value): void
    {
        if (is_array($value)) {
            $query->whereIn($filter, $value);
        } else {
            $query->where($filter, $value);
        }
    }

    /**
     * Get advertiser options (mock for now).
     */
    protected function getAdvertiserOptions(): array
    {
        // In real implementation, this would query SaaSykit Advertiser model
        return [
            1 => 'Advertiser A',
            2 => 'Advertiser B',
            3 => 'Advertiser C',
        ];
    }

    /**
     * Get campaign options.
     */
    protected function getCampaignOptions(): array
    {
        // Would query campaigns, filtered by selected advertisers if applicable
        return [
            1 => 'Campaign 1',
            2 => 'Campaign 2',
            3 => 'Campaign 3',
        ];
    }

    /**
     * Get line item options.
     */
    protected function getLineItemOptions(): array
    {
        // Would query line items, filtered by selected campaigns
        return [
            1 => 'Line Item 1',
            2 => 'Line Item 2',
            3 => 'Line Item 3',
        ];
    }

    /**
     * Get geography options.
     */
    protected function getGeographyOptions(): array
    {
        return [
            'US' => 'United States',
            'CA' => 'Canada',
            'UK' => 'United Kingdom',
            'DE' => 'Germany',
            'FR' => 'France',
        ];
    }

    /**
     * Get device options.
     */
    protected function getDeviceOptions(): array
    {
        return [
            'desktop' => 'Desktop',
            'mobile' => 'Mobile',
            'tablet' => 'Tablet',
        ];
    }

    /**
     * Get cascaded options for dependent filters.
     */
    public function getCascadedOptions(string $filter, array $selectedFilters = []): array
    {
        switch ($filter) {
            case 'campaign_id':
                return $this->getCampaignsForAdvertisers($selectedFilters['advertiser_id'] ?? []);
            case 'line_item_id':
                return $this->getLineItemsForCampaigns($selectedFilters['campaign_id'] ?? []);
            default:
                return $this->getFilterOptions()[$filter]['options'] ?? [];
        }
    }

    /**
     * Get campaigns for selected advertisers.
     */
    protected function getCampaignsForAdvertisers(array $advertiserIds): array
    {
        if (empty($advertiserIds)) {
            return $this->getCampaignOptions();
        }

        // In real implementation, query campaigns where advertiser_id in $advertiserIds
        return array_filter($this->getCampaignOptions(), function ($campaignId) use ($advertiserIds) {
            // Mock logic - in real app, campaigns belong to advertisers
            return in_array($campaignId, $advertiserIds); // Simplified mock
        });
    }

    /**
     * Get line items for selected campaigns.
     */
    protected function getLineItemsForCampaigns(array $campaignIds): array
    {
        if (empty($campaignIds)) {
            return $this->getLineItemOptions();
        }

        // Mock logic
        return array_filter($this->getLineItemOptions(), function ($lineItemId) use ($campaignIds) {
            return in_array($lineItemId, $campaignIds);
        });
    }
}
