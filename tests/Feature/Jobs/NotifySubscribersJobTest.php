<?php

use App\Actions\BitFinex\GetLastHourData;
use App\Actions\Subscriptions\CheckForPriceDeviation;
use App\Enums\CurrencyPair;
use App\Enums\TimePeriod;
use App\Jobs\NotifySubscribers;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

it('it will notify if limit is reached with no period for ', function ($pair) {
    Subscription::factory()->create([
        'pair' => $pair,
        'price' => 95925,
        'period' => TimePeriod::NotSpecified->value,
        'has_expired' => false,
    ]);

    $job = new NotifySubscribers();
    $url = config('services.bitfinex.base_url').'tickers/hist?symbols='.$pair.'&limit=1&start='.Carbon::now()->startOfHour()->timestamp * 1000;
    Http::fake([
        $url => Http::response([
            [
                0 => "tBTCUSD",
                1 => 95925,
                2 => null,
                3 => 95946,
                4 => null,
                5 => null,
                6 => null,
                7 => null,
                8 => null,
                9 => null,
                10 => null,
                11 => null,
                12 => 1739458803000,
            ]
        ], 200)
    ]);
    Notification::fake();

    $job->handle(new GetLastHourData(), new CheckForPriceDeviation());

    Notification::assertCount(1);
})->with([
    'BTC/USD' => CurrencyPair::BTC_USD->value,
    'BTC/EURO' => CurrencyPair::BTC_EURO->value,
]);

it('it will not notify if limit is not reached with no period for ', function ($pair) {
    Subscription::factory()->create([
        'pair' => $pair,
        'price' => 95925,
        'period' => TimePeriod::NotSpecified->value,
        'has_expired' => false,
    ]);

    $job = new NotifySubscribers();
    $url = config('services.bitfinex.base_url').'tickers/hist?symbols='.$pair.'&limit=1&start='.Carbon::now()->startOfHour()->timestamp * 1000;
    Http::fake([
        $url => Http::response([
            [
                0 => "tBTCUSD",
                1 => 94925,
                2 => null,
                3 => 94946,
                4 => null,
                5 => null,
                6 => null,
                7 => null,
                8 => null,
                9 => null,
                10 => null,
                11 => null,
                12 => 1739458803000,
            ]
        ], 200)
    ]);
    Notification::fake();

    $job->handle(new GetLastHourData(), new CheckForPriceDeviation());

    Notification::assertCount(0);
})->with([
    'BTC/USD' => CurrencyPair::BTC_USD->value,
    'BTC/EURO' => CurrencyPair::BTC_EURO->value,
]);

it('it will notify if bottom limit is reached when period is provided for: ', function ($pair) {
    Subscription::factory()->create([
        'pair' => $pair,
        'price' => 95925,
        'period' => TimePeriod::Hours6->value,
        'valid_from' => Carbon::now()->subHours(3)->startOfHour(),
        'has_expired' => false,
        'percentage' => 10
    ]);

    Subscription::factory()->create([
        'pair' => $pair,
        'price' => 95925,
        'period' => TimePeriod::Hours24->value,
        'valid_from' => Carbon::now()->subHours(23)->startOfHour(),
        'has_expired' => false,
    ]);

    Subscription::factory()->create([
        'pair' => $pair,
        'price' => 95925,
        'period' => TimePeriod::Hours1->value,
        'valid_from' => Carbon::now()->startOfHour(),
        'has_expired' => false,
    ]);

    $job = new NotifySubscribers();
    $url = config('services.bitfinex.base_url').'tickers/hist?symbols='.$pair.'&limit=1&start='.Carbon::now()->startOfHour()->timestamp * 1000;
    Http::fake([
        $url => Http::response([
            [
                0 => "tBTCUSD",
                1 => 85925,
                2 => null,
                3 => 85946,
                4 => null,
                5 => null,
                6 => null,
                7 => null,
                8 => null,
                9 => null,
                10 => null,
                11 => null,
                12 => 1739458803000,
            ]
        ], 200)
    ]);
    Notification::fake();

    $job->handle(new GetLastHourData(), new CheckForPriceDeviation());

    Notification::assertCount(3);
})->with([
    'BTC/USD' => CurrencyPair::BTC_USD->value,
    'BTC/EURO' => CurrencyPair::BTC_EURO->value,
]);

it('it will notify if top limit is reached when period is provided for: ', function ($pair) {
    Subscription::factory()->create([
        'pair' => $pair,
        'price' => 95925,
        'period' => TimePeriod::Hours6->value,
        'valid_from' => Carbon::now()->subHours(3)->startOfHour(),
        'has_expired' => false,
        'percentage' => 10
    ]);

    Subscription::factory()->create([
        'pair' => $pair,
        'price' => 95925,
        'period' => TimePeriod::Hours24->value,
        'valid_from' => Carbon::now()->subHours(23)->startOfHour(),
        'has_expired' => false,
    ]);

    Subscription::factory()->create([
        'pair' => $pair,
        'price' => 95925,
        'period' => TimePeriod::Hours1->value,
        'valid_from' => Carbon::now()->startOfHour(),
        'has_expired' => false,
    ]);

    $job = new NotifySubscribers();
    $url = config('services.bitfinex.base_url').'tickers/hist?symbols='.$pair.'&limit=1&start='.Carbon::now()->startOfHour()->timestamp * 1000;
    Http::fake([
        $url => Http::response([
            [
                0 => "tBTCUSD",
                1 => 105925,
                2 => null,
                3 => 105946,
                4 => null,
                5 => null,
                6 => null,
                7 => null,
                8 => null,
                9 => null,
                10 => null,
                11 => null,
                12 => 1739458803000,
            ]
        ], 200)
    ]);
    Notification::fake();

    $job->handle(new GetLastHourData(), new CheckForPriceDeviation());

    Notification::assertCount(3);
})->with([
    'BTC/USD' => CurrencyPair::BTC_USD->value,
    'BTC/EURO' => CurrencyPair::BTC_EURO->value,
]);


it('it will not notify if top limit is not reached when period is provided for: ', function ($pair) {
    Subscription::factory()->create([
        'pair' => $pair,
        'price' => 95925,
        'period' => TimePeriod::Hours6->value,
        'valid_from' => Carbon::now()->subHours(3)->startOfHour(),
        'has_expired' => false,
        'percentage' => 10
    ]);

    Subscription::factory()->create([
        'pair' => $pair,
        'price' => 95925,
        'period' => TimePeriod::Hours24->value,
        'valid_from' => Carbon::now()->subHours(23)->startOfHour(),
        'has_expired' => false,
    ]);

    Subscription::factory()->create([
        'pair' => $pair,
        'price' => 95925,
        'period' => TimePeriod::Hours1->value,
        'valid_from' => Carbon::now()->startOfHour(),
        'has_expired' => false,
    ]);

    $job = new NotifySubscribers();
    $url = config('services.bitfinex.base_url').'tickers/hist?symbols='.$pair.'&limit=1&start='.Carbon::now()->startOfHour()->timestamp * 1000;
    Http::fake([
        $url => Http::response([
            [
                0 => "tBTCUSD",
                1 => 96350,
                2 => null,
                3 => 96450,
                4 => null,
                5 => null,
                6 => null,
                7 => null,
                8 => null,
                9 => null,
                10 => null,
                11 => null,
                12 => 1739458803000,
            ]
        ], 200)
    ]);
    Notification::fake();

    $job->handle(new GetLastHourData(), new CheckForPriceDeviation());

    Notification::assertCount(0);
})->with([
    'BTC/USD' => CurrencyPair::BTC_USD->value,
    'BTC/EURO' => CurrencyPair::BTC_EURO->value,
]);

it('it will not notify if bottom limit is not reached when period is provided for: ', function ($pair) {
    Subscription::factory()->create([
        'pair' => $pair,
        'price' => 95925,
        'period' => TimePeriod::Hours6->value,
        'valid_from' => Carbon::now()->subHours(3)->startOfHour(),
        'has_expired' => false,
        'percentage' => 10
    ]);

    Subscription::factory()->create([
        'pair' => $pair,
        'price' => 95925,
        'period' => TimePeriod::Hours24->value,
        'valid_from' => Carbon::now()->subHours(23)->startOfHour(),
        'has_expired' => false,
    ]);

    Subscription::factory()->create([
        'pair' => $pair,
        'price' => 95925,
        'period' => TimePeriod::Hours1->value,
        'valid_from' => Carbon::now()->startOfHour(),
        'has_expired' => false,
    ]);

    $job = new NotifySubscribers();
    $url = config('services.bitfinex.base_url').'tickers/hist?symbols='.$pair.'&limit=1&start='.Carbon::now()->startOfHour()->timestamp * 1000;
    Http::fake([
        $url => Http::response([
            [
                0 => "tBTCUSD",
                1 => 94350,
                2 => null,
                3 => 94450,
                4 => null,
                5 => null,
                6 => null,
                7 => null,
                8 => null,
                9 => null,
                10 => null,
                11 => null,
                12 => 1739458803000,
            ]
        ], 200)
    ]);
    Notification::fake();

    $job->handle(new GetLastHourData(), new CheckForPriceDeviation());

    Notification::assertCount(0);
})->with([
    'BTC/USD' => CurrencyPair::BTC_USD->value,
    'BTC/EURO' => CurrencyPair::BTC_EURO->value,
]);
