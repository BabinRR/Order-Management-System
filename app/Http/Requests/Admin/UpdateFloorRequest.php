<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFloorRequest extends FormRequest
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
            'tables' => ['required', 'array', 'min:1'],
            'tables.*.number' => ['required', 'integer', 'min:1', 'max:100'],
            'tables.*.seats' => ['required', 'integer', 'min:1', 'max:20'],
            'tables.*.is_active' => ['sometimes', 'boolean'],
        ];
    }
}
