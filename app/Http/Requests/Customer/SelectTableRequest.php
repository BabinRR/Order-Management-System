<?php

namespace App\Http\Requests\Customer;

use App\Models\DiningTable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SelectTableRequest extends FormRequest
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
        $activeNumbers = DiningTable::activeOrdered()->pluck('number')->all();

        return [
            'table_number' => ['required', 'integer', Rule::in($activeNumbers)],
            'customer_name' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'table_number.in' => 'Please choose a valid table from the floor.',
        ];
    }
}
