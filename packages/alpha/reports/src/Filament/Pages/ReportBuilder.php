<?php

namespace Alpha\Reports\Filament\Pages;

use Filament\Pages\Page;

class ReportBuilder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string $view = 'reports::filament.pages.report-builder';

    protected static ?string $title = 'Report Builder';
}
