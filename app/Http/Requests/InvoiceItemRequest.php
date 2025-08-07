<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'description' => 'required|string|max:1000',
            'amount' => 'required|numeric|min:0',
        ];

        if ($this->isMethod('POST')) {
            $rules['invoice_id'] = 'required|exists:invoices,id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'invoice_id.required' => 'Invoice ID is required.',
            'invoice_id.exists' => 'Invoice not found.',
            'description.required' => 'Item description is required.',
            'description.max' => 'Item description may not be greater than 1000 characters.',
            'amount.required' => 'Item amount is required.',
            'amount.numeric' => 'Item amount must be a number.',
            'amount.min' => 'Item amount must be at least 0.',
        ];
    }
}
