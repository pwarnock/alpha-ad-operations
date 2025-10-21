<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Alpha Ad Operations'); ?> - Streamline Your Ad Operations</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
    body { font-family: 'Inter', sans-serif; }

        /* Modern hero gradient for AdOps SaaS */
        .hero-gradient {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 25%, #334155 50%, #0f766e 75%, #059669 100%);
            background-size: 400% 400%;
            animation: gradientShift 8s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Enhanced feature card colors */
        .feature-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(6, 182, 212, 0.1);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(6, 182, 212, 0.25);
            border-color: rgba(6, 182, 212, 0.3);
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%);
        }

        /* Colorful CTA buttons */
        .cta-primary {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            box-shadow: 0 4px 14px 0 rgba(6, 182, 212, 0.39);
        }

        .cta-primary:hover {
            background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
            box-shadow: 0 6px 20px rgba(6, 182, 212, 0.5);
            transform: translateY(-2px);
        }

        .cta-outline {
            border: 2px solid #06b6d4;
            color: #06b6d4;
            background: transparent;
        }

        .cta-outline:hover {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(6, 182, 212, 0.3);
        }

        /* Social proof section colors */
        .social-proof-bg {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        /* Testimonial section enhancements */
        .testimonial-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(6, 182, 212, 0.1);
        }

        .testimonial-card:hover {
            box-shadow: 0 10px 25px -5px rgba(6, 182, 212, 0.1);
        }

        /* Stats section colors */
        .stats-highlight {
            background: linear-gradient(135deg, #06b6d4 0%, #10b981 100%);
            color: white;
        }

        /* Final CTA section */
        .final-cta-bg {
            background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #0f766e 100%);
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <?php if (isset($component)) { $__componentOriginalf1c9f5c175e4567f47599ac6be44802c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf1c9f5c175e4567f47599ac6be44802c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.marketing.header','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('marketing.header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf1c9f5c175e4567f47599ac6be44802c)): ?>
<?php $attributes = $__attributesOriginalf1c9f5c175e4567f47599ac6be44802c; ?>
<?php unset($__attributesOriginalf1c9f5c175e4567f47599ac6be44802c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf1c9f5c175e4567f47599ac6be44802c)): ?>
<?php $component = $__componentOriginalf1c9f5c175e4567f47599ac6be44802c; ?>
<?php unset($__componentOriginalf1c9f5c175e4567f47599ac6be44802c); ?>
<?php endif; ?>

    <!-- Main Content -->
    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <?php if (isset($component)) { $__componentOriginal211506c1e29c7ecbbfb81861f611e452 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal211506c1e29c7ecbbfb81861f611e452 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.marketing.footer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('marketing.footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal211506c1e29c7ecbbfb81861f611e452)): ?>
<?php $attributes = $__attributesOriginal211506c1e29c7ecbbfb81861f611e452; ?>
<?php unset($__attributesOriginal211506c1e29c7ecbbfb81861f611e452); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal211506c1e29c7ecbbfb81861f611e452)): ?>
<?php $component = $__componentOriginal211506c1e29c7ecbbfb81861f611e452; ?>
<?php unset($__componentOriginal211506c1e29c7ecbbfb81861f611e452); ?>
<?php endif; ?>

    <!-- Scripts -->
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH /Users/peter/github/alpha/resources/views/layouts/marketing.blade.php ENDPATH**/ ?>