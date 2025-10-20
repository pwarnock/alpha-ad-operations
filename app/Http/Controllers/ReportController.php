<?php

namespace App\Http\Controllers;

use App\Models\SavedReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Spatie\LaravelPdf\Facades\Pdf;

class ReportController extends Controller
{
    public function run(SavedReport $report)
    {
        // Check if user has access to this report
        if (!$report->is_public && $report->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $this->getReportData($report);
        
        return view('reports.show', [
            'report' => $report,
            'data' => $data,
        ]);
    }

    public function exportPdf(SavedReport $report)
    {
        // Check if user has access to this report
        if (!$report->is_public && $report->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $this->getReportData($report);
        $html = $this->generatePdfHtml($data, $report);
        
        return Pdf::loadHTML($html)
            ->format('a4')
            ->name($report->name)
            ->download($report->name . '.pdf');
    }

    public function exportExcel(SavedReport $report)
    {
        // Check if user has access to this report
        if (!$report->is_public && $report->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $this->getReportData($report);
        $export = new \App\Exports\ReportExport($data, $report);
        
        return \Maatwebsite\Excel\Facades\Excel::download($export, $report->name . '.xlsx');
    }

    private function getReportData(SavedReport $report): array
    {
        $config = $report->configuration;
        $query = \App\Models\Impression::query();

        // Apply date filters
        if (!empty($config['date_from'])) {
            $query->whereDate('date', '>=', $config['date_from']);
        }
        if (!empty($config['date_to'])) {
            $query->whereDate('date', '<=', $config['date_to']);
        }

        // Apply filters based on report type
        switch ($report->report_type) {
            case 'advertiser_performance':
                if (!empty($config['advertisers'])) {
                    $query->whereHas('campaign.advertiser', function ($q) use ($config) {
                        $q->whereIn('advertisers.id', $config['advertisers']);
                    });
                }
                break;
                
            case 'campaign_delivery':
                if (!empty($config['campaigns'])) {
                    $query->whereIn('campaign_id', $config['campaigns']);
                }
                break;
                
            case 'inventory':
                if (!empty($config['ad_sizes'])) {
                    $query->whereHas('lineItem', function ($q) use ($config) {
                        $q->whereIn('ad_size', $config['ad_sizes']);
                    });
                }
                break;
        }

        // Group by configuration
        $groupBy = $config['group_by'] ?? 'day';
        switch ($groupBy) {
            case 'day':
                $query->selectRaw('DATE(date) as period, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(revenue) as revenue')
                    ->groupBy('period');
                break;
            case 'week':
                $query->selectRaw('YEARWEEK(date) as period, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(revenue) as revenue')
                    ->groupBy('period');
                break;
            case 'month':
                $query->selectRaw('DATE_FORMAT(date, "%Y-%m") as period, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(revenue) as revenue')
                    ->groupBy('period');
                break;
            case 'advertiser':
                $query->selectRaw('advertisers.name as period, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(revenue) as revenue')
                    ->join('campaigns', 'campaigns.id', '=', 'impressions.campaign_id')
                    ->join('advertisers', 'advertisers.id', '=', 'campaigns.advertiser_id')
                    ->groupBy('advertisers.id', 'advertisers.name');
                break;
            case 'campaign':
                $query->selectRaw('campaigns.name as period, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(revenue) as revenue')
                    ->join('campaigns', 'campaigns.id', '=', 'impressions.campaign_id')
                    ->groupBy('campaigns.id', 'campaigns.name');
                break;
        }

        $results = $query->get()->map(function ($item) use ($config) {
            $metrics = [];
            
            if (in_array('impressions', $config['metrics'] ?? [])) {
                $metrics['impressions'] = (int) $item->impressions;
            }
            if (in_array('clicks', $config['metrics'] ?? [])) {
                $metrics['clicks'] = (int) $item->clicks;
            }
            if (in_array('revenue', $config['metrics'] ?? [])) {
                $metrics['revenue'] = (float) $item->revenue;
            }
            if (in_array('ctr', $config['metrics'] ?? [])) {
                $metrics['ctr'] = $item->impressions > 0 ? round(($item->clicks / $item->impressions) * 100, 2) : 0;
            }
            if (in_array('ecpm', $config['metrics'] ?? [])) {
                $metrics['ecpm'] = $item->impressions > 0 ? round(($item->revenue / $item->impressions) * 1000, 2) : 0;
            }
            if (in_array('cpc', $config['metrics'] ?? [])) {
                $metrics['cpc'] = $item->clicks > 0 ? round($item->revenue / $item->clicks, 2) : 0;
            }
            
            return array_merge(['period' => $item->period], $metrics);
        });

        return $results->toArray();
    }

    private function generatePdfHtml(array $data, SavedReport $report): string
    {
        $config = $report->configuration;
        $metrics = $config['metrics'] ?? [];
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . $report->name . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #1e40af; margin-bottom: 10px; }
        .header p { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; }
        .number { text-align: right; }
        .footer { margin-top: 30px; text-align: center; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>' . $report->name . '</h1>
        <p>' . $report->report_type_label . '</p>
        <p>Period: ' . ($config['date_from'] ?? 'N/A') . ' to ' . ($config['date_to'] ?? 'N/A') . '</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Period</th>';
        
        foreach ($metrics as $metric) {
            $html .= '<th>' . ucfirst(str_replace('_', ' ', $metric)) . '</th>';
        }
        
        $html .= '</tr>
        </thead>
        <tbody>';
        
        foreach ($data as $row) {
            $html .= '<tr>
                <td>' . $row['period'] . '</td>';
            
            foreach ($metrics as $metric) {
                $value = $row[$metric] ?? 0;
                if (in_array($metric, ['ctr', 'ecpm', 'cpc'])) {
                    $html .= '<td class="number">' . $value . '%</td>';
                } elseif (in_array($metric, ['revenue', 'ecpm', 'cpc'])) {
                    $html .= '<td class="number">$' . number_format($value, 2) . '</td>';
                } else {
                    $html .= '<td class="number">' . number_format($value) . '</td>';
                }
            }
            
            $html .= '</tr>';
        }
        
        $html .= '</tbody>
    </table>
    
    <div class="footer">
        <p>Generated on ' . now()->format('Y-m-d H:i:s') . ' by Alpha Ad Operations</p>
    </div>
</body>
</html>';

        return $html;
    }
}