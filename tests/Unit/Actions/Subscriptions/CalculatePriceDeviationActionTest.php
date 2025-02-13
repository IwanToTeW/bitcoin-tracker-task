<?php

use App\Actions\Subscriptions\CalculatePriceDeviationValue;
use App\Enums\TimePeriod;
use App\Models\Subscription;

test('it throws an exception when subscription is invalid', function ($data) {
    $action = new CalculatePriceDeviationValue();
    $this->expectException(Exception::class);
    $action->execute($data);

})->with([
    'expired' => fn() => Subscription::factory(['has_expired' => true])->create(),
    'no period' => fn() => Subscription::factory(['period' => TimePeriod::NotSpecified->value])->create(),
    'no percentage' => fn() => Subscription::factory(['percentage' => 0])->create(),
    'no price' => fn() => Subscription::factory(['price' => 0])->create(),
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
