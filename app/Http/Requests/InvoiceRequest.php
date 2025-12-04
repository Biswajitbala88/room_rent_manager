<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tenant_id'          => 'required|exists:tenants,id',
            'month'              => 'required|date_format:Y-m',
            'electricity_units'  => 'required|numeric|min:0',
            'last_electric_unit' => 'required|numeric|min:0',
            'electricity_charge' => 'required|numeric|min:0',
            'water_charge'       => 'required|numeric|min:0',
        ];
    }
}
