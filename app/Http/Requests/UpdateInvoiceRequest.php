<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
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
        return [
            'tenant_id' => 'required|exists:tenants,id',
            'month' => 'required|date_format:Y-m',
            'electricity_units' => 'required|numeric',
            'electricity_charge' => 'required|numeric',
            'water_charge' => 'nullable|numeric',
            'total_amount' => 'required|numeric',
            'received_amount' => 'required|numeric',
            'payment_mode' => 'nullable|string',
            'payment_date' => 'nullable|date',
            'is_excluded' => 'nullable|boolean',
        ];
    }
}
