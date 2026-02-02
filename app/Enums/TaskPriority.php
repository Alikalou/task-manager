<?php

namespace App\Enums;

enum TaskPriority: string
// So TaskPriority is a backed enumeration type, it's backed by the string datatype.
// This is very helpful because now we can use enumeration values that convey a meaning.
{
    case Low = 'low';
    case Normal = 'normal';
    case High = 'high';
    // The enumeration cases. Note that php supports enum as an object data type, so 'Low' is actually an object.

    public static function values(): array
    {

        // This function extracts the value property of the enumeration objects, such as 'high' or 'normal'
        // self::cases is a static method that returns the set of enumeration objects that we created.
        return array_column(self::cases(), 'value');

    }
}
