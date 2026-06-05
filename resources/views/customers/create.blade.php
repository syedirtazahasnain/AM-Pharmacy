{{-- resources/views/customers/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-4">Add Customer</h2>
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            <div class="space-y-4">
                <div><label>Customer Name *</label><input type="text" name="name" required class="w-full border rounded p-2"></div>
                <div><label>District</label><input type="text" name="district" class="w-full border rounded p-2"></div>
                <div><label>Phone</label><input type="text" name="phone" class="w-full border rounded p-2"></div>
                <div><label>Address</label><textarea name="address" class="w-full border rounded p-2"></textarea></div>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Save Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection
