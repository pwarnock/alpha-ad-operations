@extends('layouts.marketing')

@section('title', 'Pricing')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-6">
            Streamline Ad Operations. Maximize Revenue.
        </h1>
        <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
            Choose the plan that fits your publishing business
        </p>
    </div>
</section>

<!-- Annual/Monthly Toggle -->
<section class="py-8 bg-gray-50 border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center space-x-4">
            <span class="text-lg font-medium {{ $billing === 'monthly' ? 'text-gray-900' : 'text-gray-500' }}" id="monthly-label">Monthly</span>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="billing-toggle" class="sr-only" {{ $billing === 'annual' ? 'checked' : '' }}>
                <div class="w-14 h-7 bg-gray-300 rounded-full transition-colors duration-200 ease-in-out">
                    <div class="dot absolute left-1 top-1 bg-white w-5 h-5 rounded-full transition-transform duration-200 ease-in-out"></div>
                </div>
            </label>
            <span class="text-lg font-medium {{ $billing === 'annual' ? 'text-gray-900' : 'text-gray-500' }}" id="annual-label">
                Annual 
                <span class="text-green-600 font-semibold">Save up to $588</span>
            </span>
        </div>
    </div>
</section>

<!-- Pricing Cards -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($plans as $plan)
                <div class="pricing-card bg-white rounded-lg shadow-lg p-8 {{ $plan['featured'] ? 'ring-2 ring-blue-500 transform scale-105' : '' }}">
                    @if($plan['featured'])
                        <div class="bg-blue-500 text-white text-sm font-semibold px-3 py-1 rounded-full inline-block mb-4">
                            MOST POPULAR
                        </div>
                    @endif
                    
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan['name'] }}</h3>
                    <p class="text-gray-600 mb-6">{{ $plan['description'] }}</p>
                    
                    <div class="mb-8">
                        @if($plan['price_monthly'] === 'Custom')
                            <span class="text-4xl font-bold text-gray-900">Custom</span>
                        @else
                            <span class="text-4xl font-bold text-gray-900">
                                ${{ $billing === 'annual' ? $plan['price_annual'] : $plan['price_monthly'] }}
                            </span>
                            <span class="text-gray-600 text-lg">
                                /{{ $billing === 'annual' ? 'year' : 'month' }}
                            </span>
                        @endif
                    </div>

                    <ul class="space-y-4 mb-8">
                        @foreach($plan['features'] as $feature)
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>

                    @if($plan['price_monthly'] === 'Custom')
                        <x-marketing.cta-button href="#" text="Contact Sales" variant="outline" class="w-full" />
                    @else
                        <x-marketing.cta-button href="{{ route('register', ['plan' => strtolower($plan['name'])]) }}" text="Start Free Trial" variant="primary" class="w-full" />
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Frequently Asked Questions
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">How does pricing scale as we grow?</h3>
                <p class="text-gray-700">
                    Our plans are designed to scale with your business. You can upgrade at any time, and we'll prorate any differences. 
                    Enterprise plans are available for large media companies with custom needs.
                </p>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Can we migrate data from our current system?</h3>
                <p class="text-gray-700">
                    Yes! We offer free migration assistance for all new customers. Our team will help you import your existing campaigns, 
                    advertisers, and historical data to ensure a smooth transition.
                </p>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">What's included in implementation?</h3>
                <p class="text-gray-700">
                    Every plan includes free setup, data migration, training for your team, and 90 days of premium support. 
                    We also provide custom integrations with your existing ad networks.
                </p>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Is there a contract commitment?</h3>
                <p class="text-gray-700">
                    No! All plans are month-to-month with no long-term contracts. Annual plans offer a discount but are completely optional. 
                    You can cancel or change your plan at any time.
                </p>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">How does billing work for multiple publications?</h3>
                <p class="text-gray-700">
                    Your plan covers all your publications under one account. There are no per-publication fees. 
                    You can add unlimited publications and users (depending on your plan tier).
                </p>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">What integrations are available?</h3>
                <p class="text-gray-700">
                    We integrate with all major ad networks including Google Ad Manager, Xandr, Magnite, and more. 
                    Custom integrations are available for Enterprise customers.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-blue-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">
            Ready to Get Started?
        </h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">
            Join hundreds of media companies that trust Alpha Ad Operations to manage their advertising business.
        </p>
        <x-marketing.cta-button href="{{ route('pricing') }}" text="Start Your 14-Day Free Trial" variant="secondary" size="xl" />
        <p class="mt-6 text-blue-200">
            No credit card required • Ready in minutes • Full feature access
        </p>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('billing-toggle');
    const monthlyLabel = document.getElementById('monthly-label');
    const annualLabel = document.getElementById('annual-label');
    const dot = toggle.nextElementSibling.querySelector('.dot');
    
    // Get current billing from URL or default to monthly
    const urlParams = new URLSearchParams(window.location.search);
    let currentBilling = urlParams.get('billing') || 'monthly';
    
    function updateBillingUI(billing) {
        if (billing === 'annual') {
            toggle.checked = true;
            dot.style.transform = 'translateX(1.25rem)';
            dot.parentElement.classList.add('bg-blue-600');
            dot.parentElement.classList.remove('bg-gray-300');
            monthlyLabel.classList.add('text-gray-500');
            monthlyLabel.classList.remove('text-gray-900');
            annualLabel.classList.add('text-gray-900');
            annualLabel.classList.remove('text-gray-500');
        } else {
            toggle.checked = false;
            dot.style.transform = 'translateX(0)';
            dot.parentElement.classList.remove('bg-blue-600');
            dot.parentElement.classList.add('bg-gray-300');
            monthlyLabel.classList.add('text-gray-900');
            monthlyLabel.classList.remove('text-gray-500');
            annualLabel.classList.add('text-gray-500');
            annualLabel.classList.remove('text-gray-900');
        }
    }
    
    // Set initial state
    updateBillingUI(currentBilling);
    
    // Handle toggle changes
    toggle.addEventListener('change', function() {
        const newBilling = this.checked ? 'annual' : 'monthly';
        updateBillingUI(newBilling);
        
        // Update URL without page reload
        const url = new URL(window.location);
        url.searchParams.set('billing', newBilling);
        window.history.replaceState({}, '', url);
    });
});
</script>
@endsection