<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:35'],
            'sort' => ['nullable', 'in:newest,oldest,due_asc,due_desc '],
            'due' => ['nullable', 'in:today,this_week,overdue'],
            'status' => ['nullable', 'in:todo,in_progress,done'],
            'priority_sort' => ['nullable', 'in:first'],

            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'distinct', 'exists:tags,id'],
        ];
    }
}
