<?php

namespace App\Exports;

use App\Models\SavedReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;

class ReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;
    protected $report;

    public function __construct(array $data, SavedReport $report)
    {
        $this->data = $data;
        $this->report = $report;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        $config = $this->report->configuration;
        $metrics = $config['metrics'] ?? [];
        
        $headings = ['Period'];
        
        foreach ($metrics as $metric) {
            $headings[] = ucfirst(str_replace('_', ' ', $metric));
        }
        
        return $headings;
    }

    public function title(): string
    {
        return $this->report->name;
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
    }
}