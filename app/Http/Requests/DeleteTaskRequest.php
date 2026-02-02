<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteTaskRequest extends FormRequest
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
        $projectId = $this->route('project')->id; // this exists because route has {project}

        return [
            'task_ids' => ['required', 'array', 'min:1'],
            'task_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('tasks', 'id')->where('project_id', $projectId),
            ],
        ];
    }

    public function messages(): array
    {

        return [
            'task_ids.required' => 'You must select at least one task to delete.',
            'task_ids.min' => 'You must select at least one task to delete.',
        ];
    }
}
