<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'room_no' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'rent_amount' => 'required|numeric',
            'is_water_charge' => 'nullable|in:0,1,on',
            'water_charge' => 'nullable|numeric',
            'is_advanced' => 'nullable|in:0,1,on',
            'parent_id' => 'nullable|exists:users,id',
            'aadhaar_image' => 'nullable',
            'aadhaar_image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_water_charge' => $this->has('is_water_charge') ? 1 : 0,
            'is_advanced' => $this->has('is_advanced') ? 1 : 0,
        ]);
    }
}
