@extends('reports::layout')

@section('title', 'Reports Dashboard')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Analytics Reports</h1>
            <p class="text-gray-600 mt-2">Advanced reporting and analytics for your advertising operations</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold">Saved Reports</h2>
                <a href="{{ route('reports.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Create New Report
                </a>
            </div>

            @if($reports->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($reports as $report)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <h3 class="font-semibold text-lg mb-2">{{ $report->name }}</h3>
                            @if($report->description)
                                <p class="text-gray-600 text-sm mb-3">{{ Str::limit($report->description, 100) }}</p>
                            @endif
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">{{ $report->chart_type }} chart</span>
                                <div class="space-x-2">
                                    <a href="{{ route('reports.show', $report) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                    <a href="{{ route('reports.edit', $report) }}" class="text-gray-600 hover:text-gray-800">Edit</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <p>No reports found. Create your first report to get started.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
