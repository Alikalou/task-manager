<?php

namespace App\Http\Requests;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            // Rule::in(array_map(fn ($e) => $e->value, TaskStatus::cases()))
            'status' => ['required', Rule::enum(TaskStatus::class)],
            'due_date' => ['nullable', 'date'],
            'sort' => ['nullable', 'in:newest,oldest,due_asc,due_desc'],
            'priority' => ['nullable', 'in:low,normal,high'],

            // Tag rules that are part of the task request
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tags,id', 'distinct'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required.',
            'title.max' => 'Task title can’t exceed 160 characters.',
            'status.in' => 'Status must be: todo, in progress, or done.',
            'due_date.date' => 'Due date must be a valid date.',
        ];
    }
}
