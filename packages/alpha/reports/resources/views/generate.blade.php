<!DOCTYPE html>
<html>
<head>
    <title>Report Results</title>
</head>
<body>
    <h1>Report Results</h1>
    <p>Filters: {{ json_encode($filters) }}</p>
    <table border="1">
        <thead>
            <tr>
                <th>Date</th>
                <th>Campaign</th>
                <th>Advertiser</th>
                <th>Line Item</th>
                <th>Impressions</th>
                <th>Clicks</th>
                <th>Revenue</th>
                <th>CTR</th>
                <th>eCPM</th>
                <th>CPC</th>
                <th>Country</th>
                <th>Device</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
            <tr>
                <td>{{ $result->date }}</td>
                <td>{{ $result->campaign->name ?? '' }}</td>
                <td>{{ $result->campaign->advertiser->name ?? '' }}</td>
                <td>{{ $result->lineItem->name ?? '' }}</td>
                <td>{{ $result->impressions }}</td>
                <td>{{ $result->clicks }}</td>
                <td>{{ $result->revenue }}</td>
                <td>{{ number_format($result->ctr, 2) }}%</td>
                <td>{{ number_format($result->ecpm, 2) }}</td>
                <td>{{ number_format($result->cpc, 2) }}</td>
                <td>{{ $result->country }}</td>
                <td>{{ $result->device }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <a href="{{ route('reports.export', ['format' => 'pdf']) }}">Export PDF</a> |
    <a href="{{ route('reports.export', ['format' => 'excel']) }}">Export Excel</a>
</body>
</html>
