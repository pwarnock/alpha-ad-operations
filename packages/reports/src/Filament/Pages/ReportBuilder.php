<?php

namespace Alpha\Reports\Filament\Pages;

use Alpha\Reports\Filament\Resources\SavedReportResource;
use Alpha\Reports\Services\ReportFilterService;
use Alpha\Reports\Services\SaaSykitReportsService;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ReportBuilder extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string $view = 'reports::filament.pages.report-builder';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 2;

    public ?array $filters = [];
    public ?array $metrics = ['impressions', 'clicks', 'revenue'];
    public ?array $groupBy = ['date'];
    public ?string $chartType = 'line';
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    public function mount(): void
    {
        $this->form->fill([
            'filters' => $this->filters,
            'metrics' => $this->metrics,
            'group_by' => $this->groupBy,
            'chart_type' => $this->chartType,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
        ]);
    }

    public function form(Form $form): Form
    {
        $filterService = app(ReportFilterService::class);
        $reportsService = app(SaaSykitReportsService::class);

        return $form
            ->schema([
                Forms\Components\Section::make('Report Configuration')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('metrics')
                                    ->label('Metrics')
                                    ->options([
                                        'impressions' => 'Impressions',
                                        'clicks' => 'Clicks',
                                        'revenue' => 'Revenue',
                                        'ctr' => 'CTR (%)',
                                        'ecpm' => 'eCPM',
                                        'cpc' => 'CPC',
                                        'fill_rate' => 'Fill Rate (%)',
                                        'viewability' => 'Viewability (%)',
                                    ])
                                    ->multiple()
                                    ->required()
                                    ->default(['impressions', 'clicks', 'revenue'])
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        // Update available chart types based on metrics
                                        $this->updateChartTypeOptions($state, $set);
                                    }),

                                Forms\Components\Select::make('chart_type')
                                    ->label('Chart Type')
                                    ->options([
                                        'line' => 'Line Chart',
                                        'bar' => 'Bar Chart',
                                        'area' => 'Area Chart',
                                        'pie' => 'Pie Chart',
                                    ])
                                    ->required()
                                    ->default('line')
                                    ->live(),
                            ]),

                        Forms\Components\Select::make('group_by')
                            ->label('Group By')
                            ->options([
                                'date' => 'Date',
                                'advertiser' => 'Advertiser',
                                'campaign' => 'Campaign',
                                'line_item' => 'Line Item',
                                'geography' => 'Geography',
                                'device_type' => 'Device Type',
                            ])
                            ->multiple()
                            ->default(['date'])
                            ->required(),
                    ]),

                Forms\Components\Section::make('Filters')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('date_from')
                                    ->label('Date From')
                                    ->default(now()->subDays(30)->format('Y-m-d')),

                                Forms\Components\DatePicker::make('date_to')
                                    ->label('Date To')
                                    ->default(now()->format('Y-m-d')),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('filters.advertiser_id')
                                    ->label('Advertisers')
                                    ->options($filterService->getFilterOptions()['advertiser_id']['options'])
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        // Cascade to campaigns
                                        $campaigns = app(ReportFilterService::class)->getCascadedOptions('campaign_id', ['advertiser_id' => $state]);
                                        $set('filters.campaign_id', []);
                                        // Note: In real implementation, you'd need to update the options dynamically
                                    }),

                                Forms\Components\Select::make('filters.campaign_id')
                                    ->label('Campaigns')
                                    ->options($filterService->getFilterOptions()['campaign_id']['options'])
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        // Cascade to line items
                                        $lineItems = app(ReportFilterService::class)->getCascadedOptions('line_item_id', ['campaign_id' => $state]);
                                        $set('filters.line_item_id', []);
                                    }),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('filters.line_item_id')
                                    ->label('Line Items')
                                    ->options($filterService->getFilterOptions()['line_item_id']['options'])
                                    ->multiple()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\Select::make('filters.geography')
                                    ->label('Geography')
                                    ->options($filterService->getFilterOptions()['geography']['options'])
                                    ->multiple()
                                    ->searchable(),

                                Forms\Components\Select::make('filters.device_type')
                                    ->label('Device Types')
                                    ->options($filterService->getFilterOptions()['device_type']['options'])
                                    ->multiple()
                                    ->searchable(),
                            ]),
                    ])
                    ->visible($reportsService->canUseAdvancedFilters()),

                Forms\Components\Section::make('Actions')
                    ->schema([
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('generate_report')
                                ->label('Generate Report')
                                ->action('generateReport')
                                ->icon('heroicon-o-chart-bar'),

                            Forms\Components\Actions\Action::make('save_report')
                                ->label('Save Report')
                                ->action('saveReport')
                                ->icon('heroicon-o-bookmark')
                                ->color('success')
                                ->visible($reportsService->canCreateReports()),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    protected function updateChartTypeOptions(array $metrics, Forms\Set $set): void
    {
        // If only one metric selected and it's categorical, suggest pie chart
        if (count($metrics) === 1 && in_array($metrics[0], ['fill_rate', 'viewability'])) {
            $set('chart_type', 'pie');
        }
    }

    public function generateReport(): void
    {
        $data = $this->form->getState();

        // Here you would generate the actual report data
        // For now, we'll just show a success notification

        Notification::make()
            ->title('Report Generated')
            ->body('Your report has been generated successfully.')
            ->success()
            ->send();
    }

    public function saveReport()
    {
        $data = $this->form->getState();

        // Create a new saved report
        $savedReport = \Alpha\Reports\Models\SavedReport::create([
            'tenant_id' => app(SaaSykitReportsService::class)->getCurrentTenantId(),
            'name' => 'Report ' . now()->format('Y-m-d H:i:s'),
            'description' => 'Generated report',
            'filters' => $data['filters'] ?? [],
            'metrics' => $data['metrics'] ?? [],
            'group_by' => $data['group_by'] ?? [],
            'date_range' => [
                'from' => $data['date_from'],
                'to' => $data['date_to'],
            ],
            'is_public' => false,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        Notification::make()
            ->title('Report Saved')
            ->body('Your report has been saved successfully.')
            ->success()
            ->send();

        // Redirect to the saved report
        return redirect()->to(SavedReportResource::getUrl('view', ['record' => $savedReport]));
    }

    public static function canAccess(): bool
    {
        $reportsService = app(SaaSykitReportsService::class);
        return $reportsService->hasFeature('basic_reports');
    }
}
