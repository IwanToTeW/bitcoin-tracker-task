<?php

use App\Actions\Subscriptions\HasSubscriptionExpired;
use App\Enums\TimePeriod;
use App\Models\Subscription;

test('it returns null when subscription is expired', closure: function ($subscription) {
    $this->assertDatabaseHas('subscriptions', [
        'id' => $subscription->id,
        'has_expired' => false
    ]);

    $action = new HasSubscriptionExpired();
    $response = $action->execute($subscription);
    $this->assertNull($response);

    $this->assertDatabaseHas('subscriptions', [
        'id' => $subscription->id,
        'has_expired' => true
    ]);
})->with([
    '24 hours subscription' => fn() => Subscription::factory()->create([
        'has_expired' => false,
        'valid_from' => now()->subHours(25)->startOfHour(),
        'period' => TimePeriod::Hours24->value
    ]),
    '6 hours subscription' => fn() => Subscription::factory()->create([
        'has_expired' => false,
        'valid_from' => now()->subHours(7)->startOfHour(),
        'period' => TimePeriod::Hours6->value
    ]),
    '1 hour subscription' => fn() => Subscription::factory()->create([
        'has_expired' => false,
        'valid_from' => now()->subHours(2)->startOfHour(),
        'period' => TimePeriod::Hours1->value
    ]),
]);

test('it returns the subscription when still active', closure: function ($subscription) {
    $this->assertDatabaseHas('subscriptions', [
        'id' => $subscription->id,
        'has_expired' => false
    ]);

    $action = new HasSubscriptionExpired();
    $response = $action->execute($subscription);
    $this->assertNotNull($response);

    $this->assertDatabaseHas('subscriptions', [
        'id' => $subscription->id,
        'has_expired' => false
    ]);
})->with([
    '24 hours subscription' => fn() => Subscription::factory()->create([
        'has_expired' => false,
        'valid_from' => now()->subHours(5)->startOfHour(),
        'period' => TimePeriod::Hours24->value
    ]),
    '6 hours subscription' => fn() => Subscription::factory()->create([
        'has_expired' => false,
        'valid_from' => now()->subHours(5)->startOfHour(),
        'period' => TimePeriod::Hours6->value
    ]),
    '1 hour subscription' => fn() => Subscription::factory()->create([
        'has_expired' => false,
        'valid_from' => now()->now()->startOfHour(),
        'period' => TimePeriod::Hours1->value
    ]),
]);

