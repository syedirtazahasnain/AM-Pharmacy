<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date|before_or_equal:today',
            'salesperson' => 'required|string|max:100',
            'remarks' => 'nullable|string|max:500',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'gst_percent' => 'required|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.stock_batch_id' => 'required|exists:stock_batches,id',
            'items.*.quantity' => 'required|integer|min:1|max:99999',
            'items.*.rate' => 'nullable|numeric|min:0.01|max:999999.99',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Add at least one product to the invoice.',
            'items.min' => 'Add at least one product to the invoice.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'date.before_or_equal' => 'Invoice date cannot be in the future.',
        ];
    }
}
