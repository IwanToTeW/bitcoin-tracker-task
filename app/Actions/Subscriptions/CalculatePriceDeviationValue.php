<?php

namespace App\Actions\Subscriptions;

use App\Models\Subscription;

class CalculatePriceDeviationValue
{
    /**
     * @throws \Exception
     */
    public function execute(Subscription $subscription): array
    {
        if ($subscription->period === 0
            || $subscription->has_expired
            || $subscription->percentage === 0
            || empty($subscription->price)
        ) {
            throw new \Exception('Subscription is invalid');
        }

        return [
            'min_price' => $subscription->price - ($subscription->price * 10 / 100),
            'max_price' => $subscription->price + ($subscription->price * 10 / 100)
        ];
    }
}
