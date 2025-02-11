<?php

namespace App\Actions\Subscriptions;

use App\Models\Subscription;

class StoreSubscriptionAction
{
    public function execute(array $data): bool
    {
        $subscription = new Subscription($data);
        return $subscription->save();
    }
}
