<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'href' => '#',
    'text' => 'Get Started',
    'variant' => 'primary',
    'size' => 'lg'
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'href' => '#',
    'text' => 'Get Started',
    'variant' => 'primary',
    'size' => 'lg'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
$baseClasses = 'inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md transition-all duration-300 cta-button';

$variantClasses = match($variant) {
    'primary' => 'text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
    'secondary' => 'text-blue-700 bg-blue-100 hover:bg-blue-200 focus:ring-blue-500',
    'outline' => 'text-blue-700 bg-transparent border-blue-600 hover:bg-blue-50 focus:ring-blue-500',
    default => 'text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500',
};

$sizeClasses = match($size) {
    'sm' => 'px-4 py-2 text-sm',
    'lg' => 'px-8 py-4 text-lg',
    'xl' => 'px-10 py-5 text-xl',
    default => 'px-6 py-3 text-base',
};

$classes = $baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses;
?>

<a href="<?php echo e($href); ?>" class="<?php echo e($classes); ?>">
    <?php echo e($text); ?>

</a><?php /**PATH /Users/peter/github/alpha/resources/views/components/marketing/cta-button.blade.php ENDPATH**/ ?>