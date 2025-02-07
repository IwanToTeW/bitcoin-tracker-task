<?php
namespace App\Actions\BitFinex;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class GetHistoryData
{
    public function execute(array $params): array
    {
        return [];
        $queryParams = [
            'symbols' => 'tBTCUSD',
            'limit' => 24
        ];
        $response = Http::bitFinex()
            ->get('tickers/hist?symbols=tBTCUSD&limit=24');

        dd($response->json());
        return $response->json();
    }
}
