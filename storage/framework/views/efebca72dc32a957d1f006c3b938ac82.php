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