<?php

namespace Alpha\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports::index');
    }

    public function generate(Request $request)
    {
        $filters = $request->only(['rep', 'advertiser', 'product', 'start_date', 'end_date']);

        $query = \App\Models\Impression::query()
            ->with(['campaign.advertiser', 'lineItem']);

        if ($filters['advertiser']) {
            $query->whereHas('campaign', function ($q) use ($filters) {
                $q->where('advertiser_id', $filters['advertiser']);
            });
        }

        if ($filters['product']) {
            // Assume product is a field in lineItem or campaign
            $query->whereHas('lineItem', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['product'] . '%');
            });
        }

        if ($filters['start_date']) {
            $query->where('date', '>=', $filters['start_date']);
        }

        if ($filters['end_date']) {
            $query->where('date', '<=', $filters['end_date']);
        }

        // For rep, assume it's a field in campaign, add later
        if ($filters['rep']) {
            $query->whereHas('campaign', function ($q) use ($filters) {
                $q->where('rep_id', $filters['rep']); // Assume rep_id added
            });
        }

        $results = $query->get();

        $data = [
            'filters' => $filters,
            'results' => $results,
        ];

        return view('reports::generate', $data);
    }

    public function export(Request $request, $format)
    {
        $filters = $request->only(['rep', 'advertiser', 'product', 'start_date', 'end_date']);

        $query = \App\Models\Impression::query()
            ->with(['campaign.advertiser', 'lineItem']);

        if ($filters['advertiser']) {
            $query->whereHas('campaign', function ($q) use ($filters) {
                $q->where('advertiser_id', $filters['advertiser']);
            });
        }

        if ($filters['product']) {
            $query->whereHas('lineItem', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['product'] . '%');
            });
        }

        if ($filters['start_date']) {
            $query->where('date', '>=', $filters['start_date']);
        }

        if ($filters['end_date']) {
            $query->where('date', '<=', $filters['end_date']);
        }

        if ($filters['rep']) {
            $query->whereHas('campaign', function ($q) use ($filters) {
                $q->where('rep_id', $filters['rep']);
            });
        }

        $results = $query->get();

        if ($format === 'pdf') {
            $pdf = \Spatie\LaravelPdf\Facades\Pdf::loadView('reports::pdf', ['results' => $results, 'filters' => $filters]);
            return $pdf->download('report.pdf');
        } elseif ($format === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \Alpha\Reports\Exports\ReportExport($results, $filters), 'report.xlsx');
        }

        return response('Invalid format', 400);
    }
}
