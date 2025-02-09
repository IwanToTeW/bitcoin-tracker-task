<?php

namespace App\Actions\BitFinex;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class GetHistoryData
{
    public function execute(array $params): Collection
    {
        $start = $params['start'] ?? Carbon::now();
        $end = $params['end'] ?? Carbon::now();

        $interval = $params['interval'] ?? 'day';


        $start = $interval === 'week' ? Carbon::parse($start)->startOfWeek() : Carbon::parse($start)->startOfDay();
        $end = $interval === 'week' ? Carbon::parse($end)->endOfWeek() : Carbon::parse($end)->endOfDay();

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
