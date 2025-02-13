<?php

namespace App\Actions\BitFinex;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class GetLastHourData
{
    public function execute(?string $pair = null): array
    {
        $response = Http::bitFinex()
            ->get($this->getEndpoint( $pair ?? 'tBTCUSD'));

        return $this->parseDailyData($response);
    }

    private function getEndpoint(string $pair): string
    {
        return 'tickers/hist?symbols='.$pair.'&limit=1&start='.Carbon::now()->startOfHour()->timestamp * 1000;
    }

    private function parseDailyData($response): array
    {
        $midPriceAction = new GetMidPrice();

        return collect($response->json())->map(function ($item) use ($midPriceAction) {
            return [
                'pair' => $item[0],
                'date' => Carbon::parse($item[12] / 1000)->format('y-m-d'),
                'hour' => Carbon::parse($item[12] / 1000)->format('H'),
                'mid' => $midPriceAction->execute($item[1], $item[3])
            ];
        })->reverse()->first();
    }
}
