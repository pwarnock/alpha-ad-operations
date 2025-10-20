@extends('reports::layout')

@section('title', $report->name . ' - Report')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $report->name }}</h1>
                    @if($report->description)
                        <p class="text-gray-600 mt-2">{{ $report->description }}</p>
                    @endif
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('reports.edit', $report) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                        Edit Report
                    </a>
                    <a href="{{ route('reports.export', [$report, 'pdf']) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        Export PDF
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <canvas id="reportChart" width="400" height="200"></canvas>
        </div>

        <div class="mt-8 bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-xl font-semibold mb-4">Report Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <strong>Chart Type:</strong> {{ ucfirst($report->chart_type) }}
                </div>
                <div>
                    <strong>Created:</strong> {{ $report->created_at->format('M j, Y') }}
                </div>
                <div>
                    <strong>Visibility:</strong> {{ $report->is_public ? 'Public' : 'Private' }}
                </div>
                <div>
                    <strong>Last Updated:</strong> {{ $report->updated_at->format('M j, Y') }}
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('reportChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: '{{ $report->chart_type }}',
            data: @json($data),
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: '{{ $report->name }}'
                    }
                }
            }
        });
    </script>
@endsection
