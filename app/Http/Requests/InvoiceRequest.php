<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
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
            'process_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:process_date',
            'customer_name' => 'required|string|max:255',
            'customer_id' => 'nullable|string',
            'customer_address' => 'nullable|string',
            'previous_balance' => 'nullable|numeric|min:0',
            'contact_person' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:255',
            'payment_account' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'notes' => 'nullable|string',
            'signature' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
        ];

        if ($this->isMethod('POST')) {
            $rules['invoice_number'] = 'required|string|unique:invoices';
        } else {
            $rules['invoice_number'] = [
                'required',
                'string',
                Rule::unique('invoices')->ignore($this->route('invoice')->id ?? null),
            ];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'invoice_number.required' => 'Invoice number is required.',
            'invoice_number.unique' => 'Invoice number has already been used.',
            'process_date.required' => 'Process date is required.',
            'process_date.date' => 'Process date must be a valid date.',
            'due_date.date' => 'Due date must be a valid date.',
            'due_date.after_or_equal' => 'Due date must be the same as or after the process date.',
            'customer_name.required' => 'Customer name is required.',
            'customer_name.max' => 'Customer name may not be greater than 255 characters.',
            'contact_person.required' => 'Contact person is required.',
            'contact_person.max' => 'Contact person may not be greater than 255 characters.',
            'contact_phone.required' => 'Contact phone is required.',
            'contact_phone.max' => 'Contact phone may not be greater than 255 characters.',
            'payment_account.required' => 'Payment account is required.',
            'payment_account.max' => 'Payment account may not be greater than 255 characters.',
            'contact_email.required' => 'Contact email is required.',
            'contact_email.email' => 'Contact email must be a valid email address.',
            'contact_email.max' => 'Contact email may not be greater than 255 characters.',
            'previous_balance.numeric' => 'Previous balance must be a number.',
            'previous_balance.min' => 'Previous balance must be at least 0.',
            'logo.image' => 'Logo must be an image file.',
            'logo.mimes' => 'Logo must be a file of type: jpeg, png, jpg, gif.',
            'logo.max' => 'Logo may not be greater than 2MB.',
            'items.required' => 'Invoice items are required.',
            'items.array' => 'Invoice items must be an array.',
            'items.min' => 'There must be at least 1 invoice item.',
            'items.*.description.required' => 'Item description is required.',
            'items.*.amount.required' => 'Item amount is required.',
            'items.*.amount.numeric' => 'Item amount must be a number.',
            'items.*.amount.min' => 'Item amount must be at least 0.',
        ];
    }
}
