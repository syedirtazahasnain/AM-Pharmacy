
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #<?php echo e($invoice->invoice_number); ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .company-name { font-size: 20px; font-weight: bold; color: #166534; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; }
        .warranty { margin-top: 30px; font-size: 9px; border-top: 1px solid #000; padding-top: 10px; }
        .signature { margin-top: 40px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">A.M PHARMACY</div>
        <div>Deals in All Kinds of Medicines Surgical & Disposable Items</div>
        <div>Tulsa Road Rawalpindi | Ph # 0307-5558892</div>
        <div>Drug Sale License No 01-374-0176-90485P | NTN NO 2392714-3</div>
    </div>

    <div>
        <p><strong>DATE:</strong> <?php echo e($invoice->date->format('d-M-Y')); ?></p>
        <p><strong>INVOICE NO:</strong> <?php echo e($invoice->invoice_number); ?></p>
        <p><strong>CUSTOMER NO:</strong> <?php echo e($invoice->customer->customer_number); ?></p>
        <p><strong><?php echo e($invoice->customer->name); ?></strong></p>
        <p><strong>DISTT:</strong> <?php echo e($invoice->customer->district); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>PRODUCT NAME</th>
                <th>MANUFACTURER</th>
                <th>BATCH</th>
                <th>PACK</th>
                <th>EXPIRY</th>
                <th>QTY</th>
                <th>MRP</th>
                <th>DISC%</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($item->product->name); ?></td>
                <td><?php echo e($item->product->manufacturer->name ?? 'N/A'); ?></td>
                <td><?php echo e($item->batch->batch_number); ?></td>
                <td><?php echo e($item->product->pack_size ?? '-'); ?></td>
                <td><?php echo e($item->batch->expiry_date->format('d-M-y')); ?></td>
                <td><?php echo e($item->quantity); ?></td>
                <td><?php echo e(number_format($item->rate, 2)); ?></td>
                <td><?php echo e($invoice->discount_percent); ?></td>
                <td><?php echo e(number_format($item->total, 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div style="text-align: right; margin-top: 20px;">
        <p><strong>Subtotal:</strong> <?php echo e(number_format($invoice->subtotal, 2)); ?></p>
        <p><strong>Discount (<?php echo e($invoice->discount_percent); ?>%):</strong> <?php echo e(number_format($invoice->discount_value, 2)); ?></p>
        <p><strong>GST (18%):</strong> <?php echo e(number_format($invoice->gst_value, 2)); ?></p>
        <p style="font-size: 14px;"><strong>TOTAL INVOICE AMOUNT:</strong> <?php echo e(number_format($invoice->total, 2)); ?></p>
    </div>

    <div class="warranty">
        <p><strong>TOTAL NO OF ITEMS:</strong> <?php echo e($invoice->items->count()); ?></p>
        <p><?php echo e($invoice->warranty_notes); ?></p>
        <p><strong>Note:</strong> A. For dated items we must be informed five months prior to expiry</p>
        <p><strong>B.</strong> This Warranty does not apply to unani, homopatic, bio, chemic, herbal and General items if any mentioned in this invoice</p>
    </div>

    <div class="signature">
        <p><strong>Signature</strong></p>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\pharmacy-app\resources\views/invoices/pdf.blade.php ENDPATH**/ ?>