{{-- resources/views/products/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Products</h2>
                <a href="{{ route('products.create') }}"
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200">
                    + Add Product
                </a>
            </div>

            <div class="overflow-x-auto">
                <table id="products-table" class="min-w-full divide-y divide-gray-200" style="width:100%">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manufacturer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pack Size</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MRP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#products-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("products.data") }}',
            type: 'GET',
            error: function(xhr, error, code) {
                console.error('DataTables error:', xhr.responseText);
                alert('Error loading products data. Please check console for details.');
            }
        },
        columns: [
            {
                data: 'name',
                name: 'name',
                render: function(data) {
                    return `<span class="font-medium text-gray-900">${escapeHtml(data)}</span>`;
                }
            },
            {
                data: 'manufacturer',
                name: 'manufacturer.name',
                render: function(data) {
                    return data ? escapeHtml(data) : '<span class="text-gray-400">N/A</span>';
                }
            },
            {
                data: 'pack_size',
                name: 'pack_size',
                render: function(data) {
                    return data ? escapeHtml(data) : '<span class="text-gray-400">-</span>';
                }
            },
            {
                data: 'mrp',
                name: 'mrp',
                render: function(data) {
                    if (!data) return '<span class="text-gray-400">-</span>';
                    return `<span class="font-semibold text-green-600">Rs. ${parseFloat(data).toFixed(2)}</span>`;
                }
            },
            {
                data: 'stock',
                name: 'available_stock',
                render: function(data) {
                    let badgeClass = 'bg-green-100 text-green-800';
                    if (data == 0) {
                        badgeClass = 'bg-red-100 text-red-800';
                        data = 'Out of Stock';
                    } else if (data < 50) {
                        badgeClass = 'bg-yellow-100 text-yellow-800';
                        data = data + ' units';
                    } else {
                        data = data + ' units';
                    }
                    return `<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${badgeClass}">${data}</span>`;
                }
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data) {
                    return data;
                }
            }
        ],
        pageLength: 40,
        lengthMenu: [[10, 25, 40, 50, 100, -1], [10, 25, 40, 50, 100, "All"]],
        order: [[0, 'asc']],
        responsive: true,
        language: {
            processing: '<div class="text-center py-4"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div><p class="mt-2 text-gray-600">Loading products...</p></div>',
            search: "Search:",
            searchPlaceholder: "Search products...",
            lengthMenu: "Show _MENU_ products per page",
            info: "Showing _START_ to _END_ of _TOTAL_ products",
            infoEmpty: "No products found",
            zeroRecords: "No matching products found",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
    });
});

// Helper function to escape HTML
function escapeHtml(text) {
    if (!text) return '';
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>
@endpush
