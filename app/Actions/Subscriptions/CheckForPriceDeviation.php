<?php

namespace App\Actions\Subscriptions;

use App\Models\Subscription;

class CheckForPriceDeviation
{
    public function execute(Subscription $subscription, array $currentPrices): bool
    {
        $hasSubscriptionExpired = new HasSubscriptionExpired();
        $calculatePriceDeviationValue = new CalculatePriceDeviationValue();

        $subscription = $hasSubscriptionExpired->execute($subscription);

        if(empty($subscription)) {
            return false;
        }

        $currentPrice = $currentPrices[$subscription->pair];
        $getTargetPrices = $calculatePriceDeviationValue->execute($subscription);

        return $currentPrice < $getTargetPrices['min_price'] || $currentPrice > $getTargetPrices['max_price'];
    }
}
