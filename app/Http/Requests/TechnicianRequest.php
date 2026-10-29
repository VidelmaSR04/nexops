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
        return [
            'nik' => ['nullable', 'string', 'max:30'],
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:50',
            'area' => 'nullable|string|max:60',
            'latitude' => 'nullable|numeric|between:-90,90|required_with:longitude',
            'longitude' => 'nullable|numeric|between:-180,180|required_with:latitude',
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+().\-\s]{6,30}$/'],
            'email' => 'nullable|email|max:255',
            'status' => 'required|string|max:30|in:Available,On Task,Offline,Cuti',
            'joined_at' => 'nullable|date',
            'exp_years' => 'nullable|integer|min:0',
            'rating' => 'nullable|numeric|between:0,5',
        ];
    }
}
