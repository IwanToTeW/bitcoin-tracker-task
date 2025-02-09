<?php

namespace App\Actions\BitFinex;

class GetMidPrice
{
    public function execute(float $bid, float $ask): float
    {
        return ($ask + $bid) / 2;
    }
}
