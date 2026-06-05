<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('invoices')->get();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'district' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string'
        ]);

        $lastCustomer = Customer::latest()->first();
        $customerNumber = $lastCustomer ? intval($lastCustomer->customer_number) + 1 : 1;

        Customer::create([
            'customer_number' => $customerNumber,
            'name' => $request->name,
            'district' => $request->district,
            'phone' => $request->phone,
            'address' => $request->address
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer added successfully');
    }
}
