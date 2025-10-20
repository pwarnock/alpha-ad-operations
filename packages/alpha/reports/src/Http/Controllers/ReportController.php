<?php

namespace Alpha\Reports\Http\Controllers;

use Alpha\Reports\Models\Report;
use Alpha\Reports\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        $user = Auth::user();
        $reports = $this->reportService->getReportsForUser($user->id, $user->tenant_id ?? 1);

        return view('reports::index', compact('reports'));
    }

    public function create()
    {
        return view('reports::create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'filters' => 'nullable|array',
            'metrics' => 'nullable|array',
            'chart_type' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['tenant_id'] = Auth::user()->tenant_id ?? 1;

        $this->reportService->createReport($validated);

        return redirect()->route('reports.index')->with('success', 'Report created successfully.');
    }

    public function show($id)
    {
        $report = Report::findOrFail($id);
        $data = $this->reportService->generateReportData($report);

        return view('reports::show', compact('report', 'data'));
    }

    public function edit($id)
    {
        return view('reports::edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // TODO: Implement report update
        return redirect()->route('reports.index');
    }

    public function destroy($id)
    {
        // TODO: Implement report deletion
        return redirect()->route('reports.index');
    }

    public function export($id, $format)
    {
        // TODO: Implement export functionality
        return response()->download(/* file path */);
    }
}
