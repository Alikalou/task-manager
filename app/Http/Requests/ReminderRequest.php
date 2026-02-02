<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'], 'delay_minutes' => ['required', 'integer', 'min:0', 'max:59'], 'delay_hours' => ['required', 'integer', 'min:0', 'max:23'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $h = (int) $this->input('delay_hours');
            $m = (int) $this->input('delay_minutes');

            if ($h === 0 && $m === 0) {
                $validator->errors()->add('delay_minutes', 'Reminder delay must be greater than 0.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'delay_hours' => 'hours',
            'delay_minutes' => 'minutes',
        ];
    }
}
