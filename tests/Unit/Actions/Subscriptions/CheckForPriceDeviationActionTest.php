<?php

use App\Actions\Subscriptions\CalculatePriceDeviationValue;
use App\Actions\Subscriptions\CheckForPriceDeviation;
use App\Enums\TimePeriod;
use App\Models\Subscription;

test('it will always return false if subscription is expired', function ($subscription) {
    $currentPrices = [$subscription->pair => 2300];
    $action = new CheckForPriceDeviation();
    $response = $action->execute($subscription, $currentPrices);
    $this->assertFalse($response);

})->with([
    'expired with price going down' => fn() => Subscription::factory([
        'price' => 2000, 'has_expired' => true, 'period' => TimePeriod::Hours6->value,
        'valid_from' => now()->subHours(7)->startOfHour(), 'percentage' => 10
    ])->create(),
    'expired with price going up' => fn() => Subscription::factory([
        'price' => 1500, 'has_expired' => true, 'period' => TimePeriod::Hours6->value,
        'valid_from' => now()->subHours(7)->startOfHour(), 'percentage' => 10
    ])->create()
]);

test('it returns min and max price when valid data is provided', function () {
    $subscription = Subscription::factory()->active()->create(['price' => 2000, 'percentage' => 10]);
    $action = new CalculatePriceDeviationValue();
    $response = $action->execute($subscription);

    $this->assertArrayHasKey('min_price', $response);
    $this->assertArrayHasKey('max_price', $response);
    $this->assertSame(1800.0, $response['min_price']);
    $this->assertSame(2200.0, $response['max_price']);
});
