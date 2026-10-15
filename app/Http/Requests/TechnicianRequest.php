<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TechnicianRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Assuming the user is authenticated and has access to technician management
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'nik' => 'required|string|max:30|unique:technicians,nik',
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:50',
            'area' => 'nullable|string|max:60',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'status' => 'required|string|max:30',
            'joined_at' => 'nullable|date',
            'exp_years' => 'nullable|integer|min:0',
            'rating' => 'nullable|numeric|between:0,5',
        ];

        // If we are updating, we need to ignore the current nik for unique rule
        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            $technicianId = $this->route('technician');
            $rules['nik'] = 'required|string|max:30|unique:technicians,nik,' . $technicianId;
        }

        return $rules;
    }
}
