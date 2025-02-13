<?php

use App\Enums\CurrencyPair;
use App\Enums\TimePeriod;
use App\Enums\ViewInterval;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;

it('can create a subscription', function () {
    $this->createUser();
    $data = [
        'email' => 'test@test.com',
        'pair' => CurrencyPair::BTC_USD->value,
        'price' => 10000,
        'period' => TimePeriod::Hours6->value,
        'percentage' => 10,
    ];

    $this->assertDatabaseMissing('subscriptions', $data);
    $this->postJson(route('api.v1.subscriptions.store'), $data)->assertStatus(200);
    $this->assertDatabaseHas('subscriptions', $data);
});

it('can not create a subscription if not authenticated', function () {
    $data = [
        'email' => 'test@test.com',
        'pair' => CurrencyPair::BTC_USD->value,
        'price' => 10000,
        'period' => TimePeriod::Hours6->value,
        'percentage' => 10,
    ];
    $this->postJson(route('api.v1.subscriptions.store'), $data)->assertStatus(401);
});

it('can not see validation error when', function (array $dataSet) {
    $this->createUser();

    $this->postJson(route('api.v1.subscriptions.store'), $dataSet['data'])
        ->assertStatus(422)
        ->assertJsonValidationErrors($dataSet['errors']);

})->with([
    'empty data' => fn() => [
        'data' => [],
        'errors' => [
            'email',
            'pair',
            'price',
        ]
    ],
    'missing email' => fn() => [
        'data' => [
            'pair' => CurrencyPair::BTC_USD->value,
            'price' => 10000,
            'period' => TimePeriod::Hours6->value,
            'percentage' => 10,
        ],
        'errors' => ['email']
    ],
    'missing pair' => fn() => [
        'data' => [
            'email' => 'test@test.com',
            'price' => 10000,
            'period' => TimePeriod::Hours6->value,
            'percentage' => 10,
        ],
        'errors' => ['pair']
    ],
    'missing period when percentage is selected' => fn() => [
        'data' => [
            'email' => 'test@test.com',
            'price' => 10000,
            'period' => TimePeriod::NotSpecified->value,
            'percentage' => 68,
        ],
        'errors' => ['period']
    ],
]);

