{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>A.M Pharmacy - @yield('title')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables CSS & JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }

        /* DataTables custom styling */
        .dataTables_wrapper .dataTables_length select {
            padding: 0.25rem 2rem 0.25rem 0.5rem;
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
            margin-left: 0.5rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.375rem 0.75rem;
            margin: 0 0.25rem;
            border-radius: 0.375rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #059669;
            color: white !important;
            border-color: #059669;
        }

        .dataTables_wrapper .dataTables_processing {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 0.5rem;
            padding: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>

    @stack('styles')
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

    @stack('scripts')
</body>
</html>
