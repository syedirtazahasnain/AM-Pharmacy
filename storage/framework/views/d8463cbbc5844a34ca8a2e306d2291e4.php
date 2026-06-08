<?php $__env->startSection('title', 'Customers'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Customers</h2>
                <a href="<?php echo e(route('customers.create')); ?>" class="bg-green-600 text-white px-4 py-2 rounded-lg">+ Add Customer</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr><th class="px-6 py-3 text-left">ID</th><th>Name</th><th>District</th><th>Phone</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr><td class="px-6 py-4"><?php echo e($customer->customer_number); ?></td>
                            <td><?php echo e($customer->name); ?></td>
                            <td><?php echo e($customer->district); ?></td>
                            <td><?php echo e($customer->phone); ?></td>
                            <td><a href="#" class="text-blue-600">View Invoices</a></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\pharmacy-app\resources\views/customers/index.blade.php ENDPATH**/ ?>