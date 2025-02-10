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
    public function execute(array $params): Collection
    {
        $interval = $params['interval'] ?? 'day';
        $isDayView = $interval === 'day';

        $start = Carbon::parse($params['date'])->startOfDay() ?? Carbon::now()->startOfDay();

        if($start->isFuture()) {
            throw(new \Exception('Date cannot be in the future'));
        }

        $end = $isDayView ? $start->copy()->endOfDay() : $start->copy()->endOfWeek();

        $startTimestamp = $start->timestamp * 1000;
        $endTimestamp = $end->timestamp * 1000;
        $pair = $params['pair'] ?? 'tBTCUSD';
        $limit = ($pair === 'day') ? 24 : 168;

        $response = Http::bitFinex()
            ->get('tickers/hist?symbols='.$pair.'&limit='.$limit.'&start='.$startTimestamp.'&end='.$endTimestamp);
        $midPriceAction = new GetMidPrice();

        return collect($response->json())->map(function ($item) use ($midPriceAction) {
            return [
                'pair' => $item[0],
                'date' => Carbon::parse($item[12] / 1000)->format('H:i'),
                'mid' => $midPriceAction->execute($item[1], $item[3]),
            ];
        })->reverse();
    }
}
