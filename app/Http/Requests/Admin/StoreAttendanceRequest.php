<?php

namespace App\Http\Requests\Admin;

use App\Models\Attendance;
use App\Models\Worker;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $waiterIds = Worker::query()
            ->whereIn('role', ['Waiter', 'Waitress'])
            ->pluck('id')
            ->all();

        return [
            'date' => ['required', 'date'],
            'attendances' => ['required', 'array', 'min:1'],
            'attendances.*.worker_id' => ['required', 'integer', Rule::in($waiterIds)],
            'attendances.*.status' => ['required', Rule::in(Attendance::statuses())],
            'attendances.*.note' => ['nullable', 'string', 'max:255'],
        ];
    }
}
