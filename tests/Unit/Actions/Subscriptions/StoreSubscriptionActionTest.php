<?php

use App\Actions\BitFinex\GetHistoryData;
use App\Actions\Subscriptions\StoreSubscription;
use App\Enums\CurrencyPair;
use App\Enums\ViewInterval;
use Illuminate\Support\Carbon;

test('it can create a subscription with valid data', function () {
    $action = new StoreSubscription();
    $data = [
        'email' => 'test@test.com',
        'pair' => CurrencyPair::BTC_USD->value,
        'price' => 10000,
        'period' => 0,
        'percentage' => 0,
    ];

    $this->assertDatabaseMissing('subscriptions', $data);
    $result = $action->execute($data);
    $this->assertTrue($result);
    $this->assertDatabaseHas('subscriptions', $data);
});

test('it can not create a subscription with: ', function (array $data) {
    $action = new StoreSubscription();
    $result = $action->execute($data);
    $this->assertFalse($result);
})->with([
    'empty data' => fn() => [],
    'missing pair' => fn() => [
        'email' => 'test@test.com',
        'price' => 10000,
        'period' => 0,
    ],
    'missing period' => fn() => [
        'email' => 'test@test.com',
        'price' => 10000,
        'pair' => CurrencyPair::BTC_USD->value,
    ],
    'missing email' => fn() => [
        'period' => 0,
        'price' => 10000,
        'pair' => CurrencyPair::BTC_USD->value,
    ],
    'missing price' => fn() => [
        'email' => 'test@test.com',
        'period' => 0,
        'pair' => CurrencyPair::BTC_USD->value,
    ],
]);
