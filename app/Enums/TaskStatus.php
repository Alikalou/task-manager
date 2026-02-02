<?php

namespace App\Enums;

enum TaskStatus: string
{
    case TO_DO = 'todo';
    case DONE = 'done';
    case IN_PROGRESS = 'in_progress';

    public static function values(): array
    {

        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {

        return match ($this) {
            self::TO_DO => 'To do',
            self::IN_PROGRESS => 'In progress',
            self::DONE => 'Done',
        };

    }
}
