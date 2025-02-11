<?php

namespace App\Enums;

enum TimePeriod: int
{
    case Hours1 = 1;
    case Hours6 = 6;
    case Hours24 = 24;
    case NotSpecified = 0;

    public static function getTimePeriodLabelsList(): array
    {
        return [
            self::Hours1->label(),
            self::Hours6->label(),
            self::Hours24->label(),
            self::NotSpecified->label(),
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Hours1 => '1 Hour',
            self::Hours6 => '6 Hours',
            self::Hours24 => '24 Hours',
            self::NotSpecified => 'Not Specified',
        };
    }

    public static function periodsRequirePercentage(): array
    {
        return [
            self::Hours1->value,
            self::Hours6->value,
            self::Hours24->value
        ];
    }
}
