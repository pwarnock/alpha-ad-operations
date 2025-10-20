<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SavedReportResource\Pages;
use App\Models\SavedReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SavedReportResource extends Resource
{
    protected static ?string $model = SavedReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationGroup = 'Reporting';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('report_type')
                            ->options([
                                'advertiser_performance' => 'Advertiser Performance',
                                'inventory' => 'Inventory Report',
                                'campaign_delivery' => 'Campaign Delivery',
                                'revenue' => 'Revenue Report',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('configuration', [])),
                        Forms\Components\Toggle::make('is_public')
                            ->label('Share with team')
                            ->default(false),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Report Filters')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('configuration.date_from')
                                    ->label('Date From')
                                    ->default(now()->subMonth()),
                                Forms\Components\DatePicker::make('configuration.date_to')
                                    ->label('Date To')
                                    ->default(now()),
                            ]),
                        
                        Forms\Components\Select::make('configuration.advertisers')
                            ->label('Advertisers')
                            ->multiple()
                            ->options(\App\Models\Advertiser::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->visible(fn (callable $get) => $get('report_type') === 'advertiser_performance'),
                        
                        Forms\Components\Select::make('configuration.campaigns')
                            ->label('Campaigns')
                            ->multiple()
                            ->options(\App\Models\Campaign::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->visible(fn (callable $get) => in_array($get('report_type'), ['campaign_delivery', 'revenue'])),
                        
                        Forms\Components\Select::make('configuration.ad_sizes')
                            ->label('Ad Sizes')
                            ->multiple()
                            ->options([
                                '728x90' => 'Leaderboard (728x90)',
                                '300x250' => 'Medium Rectangle (300x250)',
                                '160x600' => 'Wide Skyscraper (160x600)',
                                '320x50' => 'Mobile Banner (320x50)',
                                '300x600' => 'Half Page (300x600)',
                                '970x250' => 'Billboard (970x250)',
                            ])
                            ->visible(fn (callable $get) => $get('report_type') === 'inventory'),
                        
                        Forms\Components\Select::make('configuration.group_by')
                            ->label('Group By')
                            ->options([
                                'day' => 'Day',
                                'week' => 'Week',
                                'month' => 'Month',
                                'advertiser' => 'Advertiser',
                                'campaign' => 'Campaign',
                                'ad_size' => 'Ad Size',
                            ])
                            ->default('day'),
                        
                        Forms\Components\Select::make('configuration.metrics')
                            ->label('Metrics')
                            ->multiple()
                            ->options([
                                'impressions' => 'Impressions',
                                'clicks' => 'Clicks',
                                'ctr' => 'CTR (%)',
                                'revenue' => 'Revenue',
                                'ecpm' => 'eCPM',
                                'cpc' => 'CPC',
                            ]),

                    ])
                    ->collapsed()
                    ->visible(fn (callable $get) => $get('report_type')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('report_type_label')
                    ->label('Report Type')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Shared')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('report_type')
                    ->options([
                        'advertiser_performance' => 'Advertiser Performance',
                        'inventory' => 'Inventory Report',
                        'campaign_delivery' => 'Campaign Delivery',
                        'revenue' => 'Revenue Report',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('run_report')
                    ->label('Run Report')
                    ->icon('heroicon-o-play')
                    ->url(fn (SavedReport $record): string => route('reports.run', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn (SavedReport $record) => static::exportPdf($record)),
                Tables\Actions\Action::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-table-cells')
                    ->action(fn (SavedReport $record) => static::exportExcel($record)),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user'));
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSavedReports::route('/'),
            'create' => Pages\CreateSavedReport::route('/create'),
            'view' => Pages\ViewSavedReport::route('/{record}'),
            'edit' => Pages\EditSavedReport::route('/{record}/edit'),
        ];
    }

    public static function exportPdf(SavedReport $report)
    {
        $data = static::getReportData($report);
        
        return response()->streamDownload(
            function () use ($data, $report) {
                echo static::generatePdfContent($data, $report);
            },
            "{$report->name}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }

    public static function exportExcel(SavedReport $report)
    {
        $data = static::getReportData($report);
        $export = new \App\Exports\ReportExport($data, $report);
        
        return \Maatwebsite\Excel\Facades\Excel::download($export, "{$report->name}.xlsx");
    }

    private static function getReportData(SavedReport $report): array
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

    private static function generatePdfContent(array $data, SavedReport $report): string
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