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

        $start = !empty($params['date']) ? Carbon::parse($params['date'])->startOfDay() : Carbon::now()->startOfDay();

        if ($start->isFuture()) {
            throw(new \Exception('Date cannot be in the future'));
        }

        $end = $isDayView ? $start->copy()->endOfDay() : $start->copy()->endOfWeek();

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

        return collect($response->json())->map(function ($item) use ($midPriceAction){
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
            return [$date =>  round($averageMID, 2)];
        });

        return [
            'labels' => $mapped->keys(),
            'data' => $mapped->values(),
        ];
    }
}
