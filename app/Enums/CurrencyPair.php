<?php

namespace App\Enums;

enum CurrencyPair: string
{
    case BTC_USD = 'tBTCUSD';
    case BTC_EURO = 'tBTCEUR';

    public static function getPairLabelsList(): array
    {
        return [
            self::BTC_USD->label(),
            self::BTC_EURO->label(),
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::BTC_USD => 'BTC/USD',
            self::BTC_EURO => 'BTC/EURO',
        };
    }
}
