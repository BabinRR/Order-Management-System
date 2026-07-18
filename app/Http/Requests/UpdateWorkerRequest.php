<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWorkerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('workers', 'email')->ignore($this->route('worker'))],
            'phone' => ['nullable', 'string', 'max:50'],
            'shift' => ['required', Rule::in(['Morning', 'Evening', 'Night'])],
            'status' => ['required', Rule::in(['Active', 'On Leave', 'Inactive'])],
        ];
    }
}
