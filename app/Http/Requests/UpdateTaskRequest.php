<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['todo', 'in_progress', 'done'])],
            'priority' => ['nullable', 'in:low,normal,high'],

            ];
    }
}
