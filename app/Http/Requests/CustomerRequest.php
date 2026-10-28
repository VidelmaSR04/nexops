<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $customerId = $this->route('customer');

        return [
            'customer_code' => [
                'sometimes',
                'required',
                'string',
                'max:30',
                Rule::unique('customers', 'customer_code')->ignore($customerId),
            ],
            'name' => 'required|string|max:255',
            'type' => 'sometimes|required|string|in:individual,business',
            'reporter_contact' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:65535',
            'area' => 'required|string|max:60',
            'package_name' => 'required|string|max:255',
            'monthly_bill' => 'nullable|string|max:50',
            'status' => 'required|string|in:Aktif,Baru,Nonaktif',
        ];
    }
}
