<?php
namespace App\Actions\BitFinex;

use Carbon\Carbon;

class GetDayViewOptions
{
    public function execute(string $day): array
    {
        $carbon = Carbon::parse($day);
        $hours = [];
        for ($i = 0; $i < 24; $i++) {
            $hours[] = $carbon->subHour()->format('H:00');
        }
        return $hours;
    }
}
