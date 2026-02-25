<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:160'],
            'status' => ['sometimes', Rule::enum(TaskStatus::class)],
            'priority' => ['sometimes', 'in:low,normal,high'],
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $updatableFields = collect($this->only([
                'title',
                'priority',
                'status',
            ]))->filter(function ($value) {
                return ! is_null($value);
            });

            if ($updatableFields->isEmpty()) {
                $validator->errors()->add(
                    'body',
                    'At least one field must be provided for update.'
                );
            }
        });
    }
}
