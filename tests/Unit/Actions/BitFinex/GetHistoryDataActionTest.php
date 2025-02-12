<?php

use App\Actions\BitFinex\GetHistoryData;
use App\Enums\CurrencyPair;
use App\Enums\ViewInterval;
use Illuminate\Support\Carbon;

test('it can return default values even though empty query params is provided', function () {
    $action = new GetHistoryData();
    $result = $action->execute([]);
    $dataSize = sizeof($result['data']);
    $labelsSize = sizeof($result['labels']);

    $this->assertArrayHasKey('data', $result);
    $this->assertArrayHasKey('labels', $result);
    $this->assertSame($dataSize, 24);
    $this->assertSame($labelsSize, 24);
    $this->assertSame($dataSize, $labelsSize);
});

test('an exception is thrown if date in the future is provided', function () {
    $action = new GetHistoryData();
    $this->expectException(\Exception::class);
    $action->execute([
        'date' => now()->addDay()->format('Y-m-d')
    ]);
});

test('it can return data with correct params', function (array $params) {
    $action = new GetHistoryData();
    $result = $action->execute($params);

    $this->assertArrayHasKey('data', $result);
    $this->assertArrayHasKey('labels', $result);
    $this->assertSame(sizeof($result['data']), sizeof($result['labels']) );
})->with([
    'daily view tBTCUSD' => fn () =>  [
        'interval' => ViewInterval::Day->value,
        'pair' => CurrencyPair::BTC_USD->value,
        'date' => Carbon::now()->subDay()->startOfDay()->format('Y-m-d')
    ],
    'weekly view tBTCUSD' => fn () =>  [
        'interval' => ViewInterval::Week->value,
        'pair' => CurrencyPair::BTC_USD->value,
        'date' => Carbon::now()->subWeek()->startOfDay()->format('Y-m-d')
    ],
    'weekly view tBTCEUR' => fn () =>  [
        'interval' => ViewInterval::Week->value,
        'pair' => CurrencyPair::BTC_EURO->value,
        'date' => Carbon::now()->subWeek()->startOfDay()->format('Y-m-d')
    ],
    'daily view tBTCEUR' => fn () =>  [
        'interval' => ViewInterval::Day->value,
        'pair' => CurrencyPair::BTC_EURO->value,
        'date' => Carbon::now()->subWeek()->startOfDay()->format('Y-m-d')
    ],
]);
