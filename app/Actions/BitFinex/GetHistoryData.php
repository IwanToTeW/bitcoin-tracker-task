<?php

namespace App\Actions\BitFinex;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class GetHistoryData
{
    /**
     * @throws \Exception
     */
    public function execute(array $params): array
    {
        $interval = $params['interval'] ?? 'day';
        $isDayView = $interval === 'day';

        $start = $isDayView ? $this->computeStartDateForDayView($params) : $this->computeStartDateForWeekView($params);

        if ($start->isFuture()) {
            throw(new \Exception('Date cannot be in the future'));
        }

        $end = $isDayView ? $this->computeEndDateForDayView($params, $start) : $this->computeEndDateForWeekView($params, $start);
        $response = Http::bitFinex()
            ->get($this->getEndpoint($isDayView, $params['pair'] ?? 'tBTCUSD', $start->timestamp, $end->timestamp));

        return $isDayView ? $this->mapDailyDataForChart($response) : $this->mapWeeklyDataForChart($response);
    }

    private function getEndpoint(bool $isDayView, string $pair, int $startTimestamp, int $endTimestamp): string
    {
        $startTimestamp = $startTimestamp * 1000;
        $endTimestamp = $endTimestamp * 1000;

        $limit = ($pair === 'day') ? 24 : 168;
        if ($isDayView) {
            return 'tickers/hist?symbols='.$pair.'&limit='.$limit.'&start='.$startTimestamp.'&end='.$endTimestamp;
        }
        return 'tickers/hist?symbols='.$pair.'&limit='.$limit.'&end='.$endTimestamp;
    }

    private function parseDailyData($response): Collection
    {
        $midPriceAction = new GetMidPrice();

        return collect($response->json())->map(function ($item) use ($midPriceAction) {
            return [
                'pair' => $item[0],
                'date' => Carbon::parse($item[12] / 1000)->format('y-m-d'),
                'hour' => Carbon::parse($item[12] / 1000)->format('H'),
                'mid' => $midPriceAction->execute($item[1], $item[3])
            ];
        })->reverse();
    }

    private function mapDailyDataForChart($response): array
    {
        $mapped = $this->parseDailyData($response);

        return [
            'labels' => $mapped->pluck('hour'),
            'data' => $mapped->pluck('mid'),
        ];
    }

    private function mapWeeklyDataForChart($response): array
    {
        $mapped = $this->parseDailyData($response)->groupBy('date')->mapWithKeys(function ($dayCollection, $date) {
            $averageMID = $dayCollection->avg('mid'); // Calculate the average MID for the day
            return [$date => round($averageMID, 2)];
        });

        return [
            'labels' => $mapped->keys(),
            'data' => $mapped->values(),
        ];
    }

    private function computeStartDateForDayView(array $params): Carbon
    {
        $dateIsProvided = !empty($params['date']);
        if (
            $dateIsProvided && Carbon::parse($params['date'])->isToday()
            || !$dateIsProvided
        ) {
            return Carbon::now()->subDay();
        }

        return Carbon::parse($params['date'])->startOfDay();
    }

    private function computeStartDateForWeekView(array $params): Carbon
    {
        $dateIsProvided = !empty($params['date']);
        if (
            $dateIsProvided && Carbon::parse($params['date'])->isCurrentWeek()
            || !$dateIsProvided
        ) {
            return Carbon::now()->subWeek()->addDay();
        }

        return Carbon::parse($params['date'])->startOfWeek();
    }


    private function computeEndDateForDayView(array $params, Carbon $start): Carbon
    {
        $dateIsProvided = !empty($params['date']);

        if (
            $dateIsProvided && Carbon::parse($params['date'])->isToday()
            || !$dateIsProvided
        ) {
            return $start->copy()->addDay();
        }

        return Carbon::parse($params['date'])->endOfDay();
    }

    private function computeEndDateForWeekView(array $params, Carbon $start): Carbon
    {
        $dateIsProvided = !empty($params['date']);

        if (
            $dateIsProvided && Carbon::parse($params['date'])->isCurrentWeek()
            || !$dateIsProvided
        ) {
            return $start->copy()->addWeek();
        }

        return Carbon::parse($params['date'])->endOfWeek();
    }
}
