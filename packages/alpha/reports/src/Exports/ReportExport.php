<?php

namespace Alpha\Reports\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportExport implements FromCollection, WithHeadings
{
    protected $results;
    protected $filters;

    public function __construct($results, $filters)
    {
        $this->results = $results;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->results->map(function ($result) {
            return [
                'Date' => $result->date,
                'Campaign' => $result->campaign->name ?? '',
                'Advertiser' => $result->campaign->advertiser->name ?? '',
                'Line Item' => $result->lineItem->name ?? '',
                'Impressions' => $result->impressions,
                'Clicks' => $result->clicks,
                'Revenue' => $result->revenue,
                'CTR' => number_format($result->ctr, 2) . '%',
                'eCPM' => number_format($result->ecpm, 2),
                'CPC' => number_format($result->cpc, 2),
                'Country' => $result->country,
                'Device' => $result->device,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Campaign',
            'Advertiser',
            'Line Item',
            'Impressions',
            'Clicks',
            'Revenue',
            'CTR',
            'eCPM',
            'CPC',
            'Country',
            'Device',
        ];
    }
}
