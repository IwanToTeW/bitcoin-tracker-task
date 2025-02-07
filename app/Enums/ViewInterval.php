<?php

namespace App\Enums;

enum ViewInterval: string
{
    case Day = 'day';
    case Week = 'week';

    public static function getIntervalLabelsList(): array
    {
        return [
            self::Day->label(),
            self::Week->label(),
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Day => 'Day View',
            self::Week => 'Week View',
        };
    }
}
