<?php $__env->startSection('title', 'Home'); ?>

<?php $__env->startSection('content'); ?>
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
                <?php if (isset($component)) { $__componentOriginal1aa422193007bd9e0df279000994c6f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1aa422193007bd9e0df279000994c6f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.marketing.cta-button','data' => ['href' => ''.e(route('pricing')).'','text' => 'Start Free Trial','variant' => 'primary','size' => 'xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('marketing.cta-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('pricing')).'','text' => 'Start Free Trial','variant' => 'primary','size' => 'xl']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1aa422193007bd9e0df279000994c6f0)): ?>
<?php $attributes = $__attributesOriginal1aa422193007bd9e0df279000994c6f0; ?>
<?php unset($__attributesOriginal1aa422193007bd9e0df279000994c6f0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1aa422193007bd9e0df279000994c6f0)): ?>
<?php $component = $__componentOriginal1aa422193007bd9e0df279000994c6f0; ?>
<?php unset($__componentOriginal1aa422193007bd9e0df279000994c6f0); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal1aa422193007bd9e0df279000994c6f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1aa422193007bd9e0df279000994c6f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.marketing.cta-button','data' => ['href' => '#features','text' => 'See How It Works','variant' => 'outline','size' => 'xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('marketing.cta-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '#features','text' => 'See How It Works','variant' => 'outline','size' => 'xl']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1aa422193007bd9e0df279000994c6f0)): ?>
<?php $attributes = $__attributesOriginal1aa422193007bd9e0df279000994c6f0; ?>
<?php unset($__attributesOriginal1aa422193007bd9e0df279000994c6f0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1aa422193007bd9e0df279000994c6f0)): ?>
<?php $component = $__componentOriginal1aa422193007bd9e0df279000994c6f0; ?>
<?php unset($__componentOriginal1aa422193007bd9e0df279000994c6f0); ?>
<?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Social Proof Section -->
<section class="py-16 social-proof-bg">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center">
<p class="text-slate-600 mb-8 text-lg font-medium">Trusted by leading media companies</p>
<div class="grid grid-cols-2 md:grid-cols-4 gap-8 items-center">
<div class="bg-gradient-to-br from-cyan-50 to-blue-50 border border-cyan-200 rounded-xl p-6 h-20 flex items-center justify-center shadow-sm hover:shadow-md transition-all duration-300">
<span class="text-cyan-700 font-bold text-lg">MediaCorp</span>
</div>
<div class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl p-6 h-20 flex items-center justify-center shadow-sm hover:shadow-md transition-all duration-300">
<span class="text-emerald-700 font-bold text-lg">NewsHub</span>
</div>
<div class="bg-gradient-to-br from-purple-50 to-indigo-50 border border-purple-200 rounded-xl p-6 h-20 flex items-center justify-center shadow-sm hover:shadow-md transition-all duration-300">
<span class="text-purple-700 font-bold text-lg">PublishPro</span>
</div>
<div class="bg-gradient-to-br from-orange-50 to-red-50 border border-orange-200 rounded-xl p-6 h-20 flex items-center justify-center shadow-sm hover:shadow-md transition-all duration-300">
<span class="text-orange-700 font-bold text-lg">AdStream</span>
</div>
</div>
</div>
</div>
</section>

<!-- Features Section -->
<section id="features" class="py-24 bg-gradient-to-br from-slate-50 via-blue-50/30 to-emerald-50/30">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-16">
<h2 class="text-3xl md:text-5xl font-bold bg-gradient-to-r from-slate-900 via-cyan-800 to-emerald-800 bg-clip-text text-transparent mb-6">
Everything You Need to Succeed
</h2>
<p class="text-xl text-slate-600 max-w-3xl mx-auto leading-relaxed">
Our platform provides all the tools media companies need to manage their advertising operations efficiently.
</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
<div class="feature-card bg-white rounded-xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 group">
<div class="w-12 h-12 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
        </svg>
    </div>
<h3 class="text-xl font-bold text-slate-900 mb-3">Campaign Management</h3>
<p class="text-slate-600 leading-relaxed">Create, manage, and optimize advertising campaigns with our intuitive interface. Track performance in real-time and make data-driven decisions.</p>
</div>

<div class="feature-card bg-white rounded-xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 group">
<div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
    </div>
<h3 class="text-xl font-bold text-slate-900 mb-3">Real-time Reporting</h3>
<p class="text-slate-600 leading-relaxed">Get instant insights into your advertising performance with customizable reports and dashboards. Export data in multiple formats.</p>
</div>

<div class="feature-card bg-white rounded-xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 group">
<div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
        </svg>
    </div>
<h3 class="text-xl font-bold text-slate-900 mb-3">Revenue Optimization</h3>
<p class="text-slate-600 leading-relaxed">Maximize your ad revenue with advanced analytics and optimization tools. Track fill rates and identify opportunities.</p>
</div>

    <div class="feature-card bg-white rounded-xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 group">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1H9a1 1 0 00-1 1v1zm-4 7V9a1 1 0 011-1h10a1 1 0 011 1v2a1 1 0 01-1 1H8a1 1 0 01-1-1zm2 3a1 1 0 100 2h8a1 1 0 100-2H9z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Programmatic Integration</h3>
                <p class="text-slate-600 leading-relaxed">Seamlessly integrate with major ad networks and programmatic platforms. Manage all your inventory from one place.</p>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 group">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Enterprise Security</h3>
                <p class="text-slate-600 leading-relaxed">Bank-level security with SOC 2 compliance. Your data and your customers' data are always protected.</p>
            </div>

            <div class="feature-card bg-white rounded-xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 group">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-3">Lightning Fast</h3>
                <p class="text-slate-600 leading-relaxed">Built for performance with real-time data processing. Handle millions of impressions without breaking a sweat.</p>
            </div>
        </div>
    </div>
</section>

<!-- Problem/Solution Section -->
<section class="py-24 bg-gradient-to-br from-white via-cyan-50/20 to-emerald-50/20">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
<div>
<h2 class="text-3xl md:text-5xl font-bold bg-gradient-to-r from-slate-900 via-cyan-800 to-slate-900 bg-clip-text text-transparent mb-8 leading-tight">
Tired of Juggling Spreadsheets and Disconnected Tools?
</h2>
<p class="text-xl text-slate-600 mb-8 leading-relaxed">
Most media companies struggle with fragmented systems, manual reporting, and missed revenue opportunities.
Spreadsheets are error-prone, and disconnected tools make it impossible to get a complete picture of your advertising performance.
</p>
<ul class="space-y-4 mb-10">
<li class="flex items-start group">
<div class="w-8 h-8 bg-gradient-to-br from-emerald-500 to-green-600 rounded-full flex items-center justify-center mr-4 flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    </div>
    <span class="text-slate-700 text-lg font-medium">Centralized campaign and inventory management</span>
</li>
<li class="flex items-start group">
<div class="w-8 h-8 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center mr-4 flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
</div>
<span class="text-slate-700 text-lg font-medium">Automated reporting and insights</span>
</li>
<li class="flex items-start group">
    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex items-center justify-center mr-4 flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        <span class="text-slate-700 text-lg font-medium">Real-time performance tracking</span>
</li>
</ul>
<?php if (isset($component)) { $__componentOriginal1aa422193007bd9e0df279000994c6f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1aa422193007bd9e0df279000994c6f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.marketing.cta-button','data' => ['href' => ''.e(route('pricing')).'','text' => 'Start Your Free Trial','variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('marketing.cta-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('pricing')).'','text' => 'Start Your Free Trial','variant' => 'primary']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1aa422193007bd9e0df279000994c6f0)): ?>
<?php $attributes = $__attributesOriginal1aa422193007bd9e0df279000994c6f0; ?>
<?php unset($__attributesOriginal1aa422193007bd9e0df279000994c6f0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1aa422193007bd9e0df279000994c6f0)): ?>
<?php $component = $__componentOriginal1aa422193007bd9e0df279000994c6f0; ?>
<?php unset($__componentOriginal1aa422193007bd9e0df279000994c6f0); ?>
<?php endif; ?>
</div>
<div class="stats-highlight rounded-2xl p-10 shadow-xl relative overflow-hidden">
<div class="absolute inset-0 bg-gradient-to-br from-cyan-400/20 to-emerald-400/20"></div>
    <div class="relative text-center">
            <div class="text-7xl font-bold text-white mb-4 drop-shadow-lg">60%</div>
                <div class="text-2xl text-cyan-100 mb-6 font-semibold">Reduction in Ad Ops Time</div>
                    <div class="text-cyan-50 leading-relaxed">
                        Our customers save an average of 20 hours per week on advertising operations management.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonial Section -->
<section class="py-24 bg-gradient-to-br from-slate-50 via-cyan-50/30 to-blue-50/30">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="text-center mb-16">
<h2 class="text-3xl md:text-5xl font-bold bg-gradient-to-r from-slate-900 via-cyan-800 to-emerald-800 bg-clip-text text-transparent mb-6">
Loved by Media Companies
</h2>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
<div class="testimonial-card p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 group">
<div class="flex items-center mb-6">
<div class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-full flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
    <span class="text-white font-bold text-lg">SC</span>
</div>
<div>
    <div class="font-bold text-slate-900 text-lg">Sarah Chen</div>
        <div class="text-slate-600">VP of Revenue, MediaCorp</div>
    </div>
</div>
<blockquote class="text-slate-700 text-lg leading-relaxed mb-6 italic">
    "Alpha Ad Operations transformed how we manage our advertising inventory. We've seen a 40% increase in fill rates and our team spends way less time on manual reporting."
</blockquote>
<div class="flex text-amber-400 text-xl">
        ★★★★★
                </div>
</div>

<div class="testimonial-card p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 group">
<div class="flex items-center mb-6">
<div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-full flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
<span class="text-white font-bold text-lg">MJ</span>
</div>
    <div>
        <div class="font-bold text-slate-900 text-lg">Mike Johnson</div>
    <div class="text-slate-600">Director of Ad Operations, NewsHub</div>
    </div>
</div>
<blockquote class="text-slate-700 text-lg leading-relaxed mb-6 italic">
    "The reporting capabilities are game-changing. We can now create custom reports in minutes instead of hours, and the insights have helped us optimize our revenue significantly."
    </blockquote>
                <div class="flex text-amber-400 text-xl">
        ★★★★★
</div>
</div>

<div class="testimonial-card p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 group">
<div class="flex items-center mb-6">
<div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
        <span class="text-white font-bold text-lg">ED</span>
    </div>
<div>
        <div class="font-bold text-slate-900 text-lg">Emily Davis</div>
        <div class="text-slate-600">CEO, PublishPro</div>
</div>
</div>
    <blockquote class="text-slate-700 text-lg leading-relaxed mb-6 italic">
            "Finally, a platform built specifically for media companies. The integration with our existing ad networks was seamless, and the ROI was immediate."
            </blockquote>
                <div class="flex text-amber-400 text-xl">
                    ★★★★★
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="final-cta-bg text-white py-24 relative overflow-hidden">
<div class="absolute inset-0 bg-gradient-to-br from-slate-900/20 to-cyan-900/20"></div>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative">
<h2 class="text-4xl md:text-6xl font-bold mb-6 drop-shadow-lg">
    Ready to Transform Your Ad Operations?
</h2>
<p class="text-xl md:text-2xl mb-12 max-w-3xl mx-auto text-cyan-100 leading-relaxed">
    Join hundreds of media companies that have already streamlined their advertising operations with Alpha.
</p>
<div class="flex flex-col sm:flex-row gap-6 justify-center">
<?php if (isset($component)) { $__componentOriginal1aa422193007bd9e0df279000994c6f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1aa422193007bd9e0df279000994c6f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.marketing.cta-button','data' => ['href' => ''.e(route('pricing')).'','text' => 'Start 14-Day Free Trial','variant' => 'secondary','size' => 'xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('marketing.cta-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('pricing')).'','text' => 'Start 14-Day Free Trial','variant' => 'secondary','size' => 'xl']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1aa422193007bd9e0df279000994c6f0)): ?>
<?php $attributes = $__attributesOriginal1aa422193007bd9e0df279000994c6f0; ?>
<?php unset($__attributesOriginal1aa422193007bd9e0df279000994c6f0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1aa422193007bd9e0df279000994c6f0)): ?>
<?php $component = $__componentOriginal1aa422193007bd9e0df279000994c6f0; ?>
<?php unset($__componentOriginal1aa422193007bd9e0df279000994c6f0); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal1aa422193007bd9e0df279000994c6f0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1aa422193007bd9e0df279000994c6f0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.marketing.cta-button','data' => ['href' => '#','text' => 'Schedule a Demo','variant' => 'outline','size' => 'xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('marketing.cta-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => '#','text' => 'Schedule a Demo','variant' => 'outline','size' => 'xl']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1aa422193007bd9e0df279000994c6f0)): ?>
<?php $attributes = $__attributesOriginal1aa422193007bd9e0df279000994c6f0; ?>
<?php unset($__attributesOriginal1aa422193007bd9e0df279000994c6f0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1aa422193007bd9e0df279000994c6f0)): ?>
<?php $component = $__componentOriginal1aa422193007bd9e0df279000994c6f0; ?>
<?php unset($__componentOriginal1aa422193007bd9e0df279000994c6f0); ?>
<?php endif; ?>
</div>
<p class="mt-8 text-cyan-200 text-lg">
    No credit card required • Setup in minutes • Cancel anytime
    </p>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.marketing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/peter/github/alpha/resources/views/marketing/home.blade.php ENDPATH**/ ?>