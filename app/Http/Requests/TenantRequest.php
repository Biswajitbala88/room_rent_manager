<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TenantRequest extends FormRequest
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
            'name'            => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'room_no'         => 'nullable|string|max:50',
            'start_date'      => 'nullable|date',
            'rent_amount'     => 'nullable|numeric|min:0',
            'is_water_charge' => 'nullable',
            'parent_id'       => 'nullable|integer',
            'water_charge'    => 'nullable|numeric|min:0',
            'is_advanced'     => 'nullable',
        ];
    }
}
