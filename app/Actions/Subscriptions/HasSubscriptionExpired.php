<?php

namespace App\Actions\Subscriptions;

use App\Models\Subscription;
use Illuminate\Support\Carbon;

class HasSubscriptionExpired
{
    public function execute(Subscription $subscription): ?Subscription
    {
        if ($subscription->expired || $subscription->period === 0) {
            return null;
        }

        $validFrom = Carbon::parse($subscription->valid_from);
        if (Carbon::now()->greaterThan($validFrom->addHours($subscription->period))) {
            $subscription->has_expired = true;
            $subscription->save();
            return null;
        }

        return $subscription;
    }
}
