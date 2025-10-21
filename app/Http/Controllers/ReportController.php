<?php

namespace App\Http\Controllers;

use App\Models\SavedReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Spatie\LaravelPdf\Facades\Pdf;

class ReportController extends Controller
{
    public function show($saved_report)
    {
        $report = SavedReport::findOrFail($saved_report);
        
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

    public function run($saved_report)
    {
        $report = SavedReport::findOrFail($saved_report);
        
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

    public function exportPdf($saved_report)
    {
        $report = SavedReport::findOrFail($saved_report);
        
        // Check if user has access to this report
        if (!$report->is_public && $report->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $this->getReportData($report);
        $html = $this->generatePdfHtml($data, $report);
        
        try {
            // Try DomPDF first (lighter, no external dependencies)
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            return $pdf->download($report->name . '.pdf');
        } catch (\Exception $e) {
            // Fallback to HTML if PDF generation fails
            return response($html)
                ->header('Content-Type', 'text/html')
                ->header('Content-Disposition', 'attachment; filename="' . $report->name . '.html"');
        }
    }

    public function exportHtml($saved_report)
    {
        $report = SavedReport::findOrFail($saved_report);
        
        // Check if user has access to this report
        if (!$report->is_public && $report->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $this->getReportData($report);
        $html = $this->generatePdfHtml($data, $report);
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $report->name . '.html"');
    }

    public function exportExcel($saved_report)
    {
        $report = SavedReport::findOrFail($saved_report);
        
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
        $filters = $config['filters'] ?? [];
        $query = \App\Models\Impression::query();

        // Apply date filters
        if (!empty($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        // Apply advertiser filters
        if (!empty($filters['advertiser_id'])) {
            $query->whereHas('campaign.advertiser', function ($q) use ($filters) {
                $q->whereIn('advertisers.id', $filters['advertiser_id']);
            });
        }

        // Apply campaign filters
        if (!empty($filters['campaign_id'])) {
            $query->whereIn('campaign_id', $filters['campaign_id']);
        }

        // Apply line item filters
        if (!empty($filters['line_item_id'])) {
            $query->whereIn('line_item_id', $filters['line_item_id']);
        }

        // Apply user (sales rep) filters
        if (!empty($filters['user_id'])) {
            $query->whereIn('user_id', $filters['user_id']);
        }

        // Apply product filters
        if (!empty($filters['product_id'])) {
            $query->whereHas('campaign.product', function ($q) use ($filters) {
                $q->whereIn('products.id', $filters['product_id']);
            });
        }

        // Group by configuration
        $groupBy = $config['group_by'] ?? ['date'];
        // Handle both string and array formats for backward compatibility
        if (is_string($groupBy)) {
            $groupBy = [$groupBy];
        }

        foreach ($groupBy as $groupByField) {
            switch ($groupByField) {
                case 'date':
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
                case 'line_item':
                    $query->selectRaw('line_items.name as period, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(revenue) as revenue')
                        ->join('line_items', 'line_items.id', '=', 'impressions.line_item_id')
                        ->groupBy('line_items.id', 'line_items.name');
                    break;
                case 'user':
                    $query->selectRaw('users.name as period, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(revenue) as revenue')
                        ->leftJoin('users', 'users.id', '=', 'impressions.user_id')
                        ->groupBy('users.id', 'users.name');
                    break;
                case 'product':
                    $query->selectRaw('products.name as period, SUM(impressions) as impressions, SUM(clicks) as clicks, SUM(revenue) as revenue')
                        ->join('campaigns', 'campaigns.id', '=', 'impressions.campaign_id')
                        ->leftJoin('products', 'products.id', '=', 'campaigns.product_id')
                        ->groupBy('products.id', 'products.name');
                    break;
            }
        }

        $results = $query->get()->map(function ($item) use ($config) {
            $metrics = [];
            $metricsConfig = $config['metrics'] ?? ['impressions', 'clicks', 'revenue'];
            
            if (in_array('impressions', $metricsConfig)) {
                $metrics['impressions'] = (int) $item->impressions;
            }
            if (in_array('clicks', $metricsConfig)) {
                $metrics['clicks'] = (int) $item->clicks;
            }
            if (in_array('revenue', $metricsConfig)) {
                $metrics['revenue'] = (float) $item->revenue;
            }
            if (in_array('ctr', $metricsConfig)) {
                $metrics['ctr'] = $item->impressions > 0 ? round(($item->clicks / $item->impressions) * 100, 2) : 0;
            }
            if (in_array('ecpm', $metricsConfig)) {
                $metrics['ecpm'] = $item->impressions > 0 ? round(($item->revenue / $item->impressions) * 1000, 2) : 0;
            }
            if (in_array('cpc', $metricsConfig)) {
                $metrics['cpc'] = $item->clicks > 0 ? round($item->revenue / $item->clicks, 2) : 0;
            }
            
            return array_merge(['period' => $item->period], $metrics);
        });

        return $results->toArray();
    }

    private function generatePdfHtml(array $data, SavedReport $report): string
    {
        $config = $report->configuration;
        $filters = $config['filters'] ?? [];
        $metrics = $config['metrics'] ?? ['impressions', 'clicks', 'revenue'];
        
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
        <p>Period: ' . ($filters['date_from'] ?? 'N/A') . ' to ' . ($filters['date_to'] ?? 'N/A') . '</p>
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