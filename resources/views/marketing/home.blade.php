@extends('layouts.marketing')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Streamline Ad Operations.<br>
                <span class="text-yellow-300">Maximize Revenue.</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                The comprehensive SaaS platform designed for media companies to manage campaigns, 
                track performance, and optimize revenue all in one place.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <x-marketing.cta-button href="{{ route('pricing') }}" text="Start Free Trial" variant="primary" size="xl" />
                <x-marketing.cta-button href="#features" text="See How It Works" variant="outline" size="xl" />
            </div>
        </div>
    </div>
</section>

<!-- Social Proof Section -->
<section class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <p class="text-gray-600 mb-8">Trusted by leading media companies</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 items-center">
                <div class="bg-gray-100 rounded-lg p-4 h-20 flex items-center justify-center">
                    <span class="text-gray-600 font-semibold">MediaCorp</span>
                </div>
                <div class="bg-gray-100 rounded-lg p-4 h-20 flex items-center justify-center">
                    <span class="text-gray-600 font-semibold">NewsHub</span>
                </div>
                <div class="bg-gray-100 rounded-lg p-4 h-20 flex items-center justify-center">
                    <span class="text-gray-600 font-semibold">PublishPro</span>
                </div>
                <div class="bg-gray-100 rounded-lg p-4 h-20 flex items-center justify-center">
                    <span class="text-gray-600 font-semibold">AdStream</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Everything You Need to Succeed
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Our platform provides all the tools media companies need to manage their advertising operations efficiently.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <x-marketing.feature-card 
                icon="heroicon-o-rectangle-stack"
                title="Campaign Management"
                description="Create, manage, and optimize advertising campaigns with our intuitive interface. Track performance in real-time and make data-driven decisions."
            />
            <x-marketing.feature-card 
                icon="heroicon-o-chart-bar"
                title="Real-time Reporting"
                description="Get instant insights into your advertising performance with customizable reports and dashboards. Export data in multiple formats."
            />
            <x-marketing.feature-card 
                icon="heroicon-o-currency-dollar"
                title="Revenue Optimization"
                description="Maximize your ad revenue with advanced analytics and optimization tools. Track fill rates and identify opportunities."
            />
            <x-marketing.feature-card 
                icon="heroicon-o-puzzle"
                title="Programmatic Integration"
                description="Seamlessly integrate with major ad networks and programmatic platforms. Manage all your inventory from one place."
            />
            <x-marketing.feature-card 
                icon="heroicon-o-shield-check"
                title="Enterprise Security"
                description="Bank-level security with SOC 2 compliance. Your data and your customers' data are always protected."
            />
            <x-marketing.feature-card 
                icon="heroicon-o-lightning-bolt"
                title="Lightning Fast"
                description="Built for performance with real-time data processing. Handle millions of impressions without breaking a sweat."
            />
        </div>
    </div>
</section>

<!-- Problem/Solution Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                    Tired of Juggling Spreadsheets and Disconnected Tools?
                </h2>
                <p class="text-lg text-gray-600 mb-6">
                    Most media companies struggle with fragmented systems, manual reporting, and missed revenue opportunities. 
                    Spreadsheets are error-prone, and disconnected tools make it impossible to get a complete picture of your advertising performance.
                </p>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-700">Centralized campaign and inventory management</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-700">Automated reporting and insights</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-700">Real-time performance tracking</span>
                    </li>
                </ul>
                <x-marketing.cta-button href="{{ route('pricing') }}" text="Start Your Free Trial" variant="primary" />
            </div>
            <div class="bg-gray-100 rounded-lg p-8">
                <div class="text-center">
                    <div class="text-6xl font-bold text-blue-600 mb-2">60%</div>
                    <div class="text-xl text-gray-600 mb-4">Reduction in Ad Ops Time</div>
                    <div class="text-gray-500">
                        Our customers save an average of 20 hours per week on advertising operations management.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonial Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Loved by Media Companies
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-4">
                    <img src="https://picsum.photos/seed/avatar1/48/48.jpg" alt="Sarah Chen" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <div class="font-semibold text-gray-900">Sarah Chen</div>
                        <div class="text-sm text-gray-600">VP of Revenue, MediaCorp</div>
                    </div>
                </div>
                <blockquote class="text-gray-700 italic mb-4">
                    "Alpha Ad Operations transformed how we manage our advertising inventory. We've seen a 40% increase in fill rates and our team spends way less time on manual reporting."
                </blockquote>
                <div class="text-yellow-400">
                    ★★★★★
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-4">
                    <img src="https://picsum.photos/seed/avatar2/48/48.jpg" alt="Mike Johnson" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <div class="font-semibold text-gray-900">Mike Johnson</div>
                        <div class="text-sm text-gray-600">Director of Ad Operations, NewsHub</div>
                    </div>
                </div>
                <blockquote class="text-gray-700 italic mb-4">
                    "The reporting capabilities are game-changing. We can now create custom reports in minutes instead of hours, and the insights have helped us optimize our revenue significantly."
                </blockquote>
                <div class="text-yellow-400">
                    ★★★★★
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-4">
                    <img src="https://picsum.photos/seed/avatar3/48/48.jpg" alt="Emily Davis" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <div class="font-semibold text-gray-900">Emily Davis</div>
                        <div class="text-sm text-gray-600">CEO, PublishPro</div>
                    </div>
                </div>
                <blockquote class="text-gray-700 italic mb-4">
                    "Finally, a platform built specifically for media companies. The integration with our existing ad networks was seamless, and the ROI was immediate."
                </blockquote>
                <div class="text-yellow-400">
                    ★★★★★★
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-blue-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">
            Ready to Transform Your Ad Operations?
        </h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">
            Join hundreds of media companies that have already streamlined their advertising operations with Alpha.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <x-marketing.cta-button href="{{ route('pricing') }}" text="Start 14-Day Free Trial" variant="secondary" size="xl" />
            <x-marketing.cta-button href="#" text="Schedule a Demo" variant="outline" size="xl" />
        </div>
        <p class="mt-6 text-blue-200">
            No credit card required • Setup in minutes • Cancel anytime
        </p>
    </div>
</section>
@endsection