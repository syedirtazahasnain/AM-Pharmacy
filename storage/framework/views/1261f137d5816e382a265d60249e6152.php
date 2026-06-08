
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>A.M Pharmacy - <?php echo $__env->yieldContent('title'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-xl font-bold text-green-700">A.M PHARMACY</span>
                    </div>
                    <div class="hidden md:ml-6 md:flex md:space-x-8">
                        <a href="<?php echo e(route('dashboard')); ?>" class="text-gray-900 hover:text-green-600 px-3 py-2 text-sm font-medium">Dashboard</a>
                        <a href="<?php echo e(route('products.index')); ?>" class="text-gray-500 hover:text-green-600 px-3 py-2 text-sm font-medium">Products</a>
                        <a href="<?php echo e(route('customers.index')); ?>" class="text-gray-500 hover:text-green-600 px-3 py-2 text-sm font-medium">Customers</a>
                        <a href="<?php echo e(route('invoices.create')); ?>" class="bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-lg text-sm font-medium">+ New Invoice</a>
                        <a href="<?php echo e(route('invoices.index')); ?>" class="text-gray-500 hover:text-green-600 px-3 py-2 text-sm font-medium">Invoices</a>
                        <a href="<?php echo e(route('stock.index')); ?>" class="text-gray-500 hover:text-green-600 px-3 py-2 text-sm font-medium">Stock Report</a>
                    </div>
                </div>

                
                <div class="flex items-center space-x-4">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center">
                                <span class="text-white text-sm font-medium"><?php echo e(substr(Auth::user()->name ?? 'U', 0, 1)); ?></span>
                            </div>
                            <span class="hidden md:inline text-sm"><?php echo e(Auth::user()->name ?? 'User'); ?></span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        
                        <div x-show="open"
                             @click.away="open = false"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                             style="display: none;">
                            <a href="<?php echo e(route('profile.edit')); ?>"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Profile Settings
                            </a>
                            <hr class="my-1">
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-6">
        <?php if(session('success')): ?>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <?php echo e(session('success')); ?>

                </div>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <?php echo e(session('error')); ?>

                </div>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>

    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
<?php /**PATH C:\laragon\www\pharmacy-app\resources\views/layouts/app.blade.php ENDPATH**/ ?>