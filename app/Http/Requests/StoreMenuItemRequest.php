<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMenuItemRequest extends FormRequest
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
            'category' => ['required', Rule::in(['Starters', 'Mains', 'Desserts', 'Drinks', 'Sides'])],
            'price' => ['required', 'integer', 'min:0', 'max:1000000'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['Available', 'Sold Out', 'Hidden'])],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
