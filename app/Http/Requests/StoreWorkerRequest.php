<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreWorkerRequest extends FormRequest
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
        $loginRole = in_array($this->input('role'), ['Waiter', 'Waitress'], true);

        return [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('workers', 'email'),
                Rule::unique('users', 'email'),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'shift' => ['required', Rule::in(['Morning', 'Evening', 'Night'])],
            'status' => ['required', Rule::in(['Active', 'On Leave', 'Inactive'])],
            'password' => [$loginRole ? 'required' : 'nullable', 'confirmed', Password::defaults()],
        ];
    }
}
