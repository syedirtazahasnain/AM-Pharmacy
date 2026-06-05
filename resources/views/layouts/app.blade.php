{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A.M Pharmacy - @yield('title')</title>
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
                        <a href="{{ route('dashboard') }}" class="text-gray-900 hover:text-green-600 px-3 py-2 text-sm font-medium">Dashboard</a>
                        <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-green-600 px-3 py-2 text-sm font-medium">Products</a>
                        <a href="{{ route('customers.index') }}" class="text-gray-500 hover:text-green-600 px-3 py-2 text-sm font-medium">Customers</a>
                        <a href="{{ route('invoices.create') }}" class="bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-lg text-sm font-medium">+ New Invoice</a>
                        <a href="{{ route('invoices.index') }}" class="text-gray-500 hover:text-green-600 px-3 py-2 text-sm font-medium">Invoices</a>
                        <a href="{{ route('stock.index') }}" class="text-gray-500 hover:text-green-600 px-3 py-2 text-sm font-medium">Stock Report</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-6">
        @if(session('success'))
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
