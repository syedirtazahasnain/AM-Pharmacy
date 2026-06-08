<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Today's Sales</div>
            <div class="text-2xl font-bold text-green-600">Rs. <?php echo e(number_format($todaySales, 2)); ?></div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Monthly Sales</div>
            <div class="text-2xl font-bold text-blue-600">Rs. <?php echo e(number_format($monthlySales, 2)); ?></div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Total Products</div>
            <div class="text-2xl font-bold text-purple-600"><?php echo e($totalProducts); ?></div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Total Customers</div>
            <div class="text-2xl font-bold text-orange-600"><?php echo e($totalCustomers); ?></div>
        </div>
    </div>

    <!-- Alerts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php if($expiringSoon->count() > 0): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h3 class="font-semibold text-yellow-800 mb-2">⚠️ Expiring Soon (3 months)</h3>
            <ul>
                <?php $__currentLoopData = $expiringSoon; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="text-sm text-yellow-700">
                    <?php echo e($batch->product->name); ?> - Batch: <?php echo e($batch->batch_number); ?>

                    (Expires: <?php echo e($batch->expiry_date->format('d-m-Y')); ?>) - Qty: <?php echo e($batch->quantity); ?>

                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if($lowStock->count() > 0): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="font-semibold text-red-800 mb-2">🔴 Low Stock Alert</h3>
            <ul>
                <?php $__currentLoopData = $lowStock; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $batch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="text-sm text-red-700">
                    <?php echo e($batch->product->name); ?> - Batch: <?php echo e($batch->batch_number); ?>

                    (Only <?php echo e($batch->quantity); ?> left)
                </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\pharmacy-app\resources\views/dashboard.blade.php ENDPATH**/ ?>