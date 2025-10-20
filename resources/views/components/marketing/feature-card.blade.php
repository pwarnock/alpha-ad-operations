@props([
    'icon' => 'heroicon-o-rectangle-stack',
    'title' => 'Feature Title',
    'description' => 'Feature description goes here.',
])

<div class="feature-card bg-white p-6 rounded-lg shadow-md hover:shadow-xl">
    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg mb-4">
        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            @if($icon === 'heroicon-o-rectangle-stack')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            @elseif($icon === 'heroicon-o-chart-bar')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path>
            @elseif($icon === 'heroicon-o-currency-dollar')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            @elseif($icon === 'heroicon-o-puzzle')
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 001 1v1a2 2 0 11-4 0v-1a1 1 0 00-1-1H7a1 1 0 00-1 1v-1a2 2 0 11-4 0v1a1 1 0 00-1 1H4a1 1 0 01-1-1v-1a2 2 0 11-4 0v-1a1 1 0 00-1-1H2a1 1 0 01-1-1V7a1 1 0 011-1h3a1 1 0 001-1v-1a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 001 1v1a2 2 0 11-4 0v-1a1 1 0 00-1-1H7a1 1 0 00-1 1v-1a2 2 0 11-4 0v1a1 1 0 001 1h3a1 1 0 001 1v1a2 2 0 11-4 0v-1a1 1 0 00-1-1H2a1 1 0 01-1-1V7a1 1 0 011-1h3a1 1 0 001-1v-1z"></path>
            @else
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            @endif
        </svg>
    </div>
    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $title }}</h3>
    <p class="text-gray-600">{{ $description }}</p>
</div>