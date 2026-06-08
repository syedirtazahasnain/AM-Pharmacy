<?php $__env->startSection('title', 'Invoice #' . $invoice->invoice_number); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto">
    <div class="bg-white p-8 rounded-lg shadow">
        <div class="text-center border-b pb-4 mb-6">
            <h2 class="text-3xl font-bold text-green-700">A.M PHARMACY</h2>
            <p class="text-sm">Deals in All Kinds of Medicines Surgical & Disposable Items</p>
            <p class="text-sm">Tulsa Road Rawalpindi | Ph: 0307-5558892</p>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p><strong>Invoice No:</strong> <?php echo e($invoice->invoice_number); ?></p>
                <p><strong>Date:</strong> <?php echo e($invoice->date->format('d-M-Y')); ?></p>
                <p><strong>Customer:</strong> <?php echo e($invoice->customer->name); ?></p>
                <p><strong>District:</strong> <?php echo e($invoice->customer->district); ?></p>
            </div>
        </div>

        <table class="w-full border-collapse border">
            <thead class="bg-gray-100">
                <tr><th class="border p-2">Product</th><th class="border p-2">Batch</th><th class="border p-2">Qty</th><th class="border p-2">Rate</th><th class="border p-2">Total</th></tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="border p-2"><?php echo e($item->product->name); ?></td>
                    <td class="border p-2"><?php echo e($item->batch->batch_number); ?></td>
                    <td class="border p-2 text-center"><?php echo e($item->quantity); ?></td>
                    <td class="border p-2 text-right">Rs. <?php echo e(number_format($item->rate, 2)); ?></td>
                    <td class="border p-2 text-right">Rs. <?php echo e(number_format($item->total, 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <div class="text-right mt-4">
            <p>Subtotal: Rs. <?php echo e(number_format($invoice->subtotal, 2)); ?></p>
            <p>Discount (<?php echo e($invoice->discount_percent); ?>%): Rs. <?php echo e(number_format($invoice->discount_value, 2)); ?></p>
            <p>GST (18%): Rs. <?php echo e(number_format($invoice->gst_value, 2)); ?></p>
            <p class="text-xl font-bold">Total: Rs. <?php echo e(number_format($invoice->total, 2)); ?></p>
        </div>

        <div class="text-xs text-gray-600 mt-8 border-t pt-4">
            <p><?php echo e($invoice->warranty_notes); ?></p>
            <p class="mt-2"><strong>Note:</strong> A. For dated items we must be informed five months prior to expiry</p>
            <p class="mt-4 text-right"><strong>Signature</strong></p>
        </div>

        <div class="mt-6 flex justify-between">
            <a href="<?php echo e(route('invoices.index')); ?>" class="bg-gray-500 text-white px-4 py-2 rounded">Back</a>
            <a href="<?php echo e(route('invoices.print', $invoice)); ?>" class="bg-green-600 text-white px-4 py-2 rounded">Download PDF</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\pharmacy-app\resources\views/invoices/show.blade.php ENDPATH**/ ?>