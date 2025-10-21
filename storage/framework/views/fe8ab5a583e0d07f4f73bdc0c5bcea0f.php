<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Report Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo e($report->name); ?></h1>
            <p class="text-lg text-gray-600 mb-4"><?php echo e($report->report_type_label); ?></p>
            <p class="text-gray-500">
                Period: <?php echo e($report->configuration['date_from'] ?? 'N/A'); ?> to <?php echo e($report->configuration['date_to'] ?? 'N/A'); ?>

            </p>
        </div>

        <!-- Export Actions -->
        <div class="mb-6 flex justify-center space-x-4">
            <form method="POST" action="<?php echo e(route('reports.export.pdf', $report)); ?>" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export PDF
                </button>
            </form>
            
            <form method="POST" action="<?php echo e(route('reports.export.html', $report)); ?>" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                    Export HTML
                </button>
            </form>
            
            <form method="POST" action="<?php echo e(route('reports.export.excel', $report)); ?>" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v6m3-2h6"></path>
                    </svg>
                    Export Excel
                </button>
            </form>
        </div>

        <!-- Report Data Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Period
                        </th>
                        <?php $__currentLoopData = $report->configuration['metrics'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <?php echo e(ucfirst(str_replace('_', ' ', $metric))); ?>

                            </th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo e($row['period']); ?>

                            </td>
                            <?php $__currentLoopData = $report->configuration['metrics'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                    <?php if(in_array($metric, ['ctr', 'ecpm', 'cpc'])): ?>
                                        <?php echo e($row[$metric] ?? 0); ?>%
                                    <?php elseif(in_array($metric, ['revenue', 'ecpm', 'cpc'])): ?>
                                        $<?php echo e(number_format($row[$metric] ?? 0, 2)); ?>

                                    <?php else: ?>
                                        <?php echo e(number_format($row[$metric] ?? 0)); ?>

                                    <?php endif; ?>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <!-- Summary Statistics -->
        <?php if(count($data) > 0): ?>
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php $__currentLoopData = $report->configuration['metrics'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($metric === 'impressions'): ?>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-blue-600">Total Impressions</div>
                            <div class="text-2xl font-bold text-blue-900">
                                <?php echo e(number_format(array_sum(array_column($data, 'impressions')))); ?>

                            </div>
                        </div>
                    <?php elseif($metric === 'clicks'): ?>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-green-600">Total Clicks</div>
                            <div class="text-2xl font-bold text-green-900">
                                <?php echo e(number_format(array_sum(array_column($data, 'clicks')))); ?>

                            </div>
                        </div>
                    <?php elseif($metric === 'revenue'): ?>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-yellow-600">Total Revenue</div>
                            <div class="text-2xl font-bold text-yellow-900">
                                $<?php echo e(number_format(array_sum(array_column($data, 'revenue')), 2)); ?>

                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/peter/github/alpha/resources/views/reports/show.blade.php ENDPATH**/ ?>