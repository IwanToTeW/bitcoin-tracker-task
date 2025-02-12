<?php

namespace App\Actions\Subscriptions;

use App\Models\Subscription;
use Illuminate\Support\Facades\Log;

class StoreSubscriptionAction
{
    public function execute(array $data): bool
    {
        try {
            $subscription = new Subscription($data);
            return $subscription->save();
        } catch (\Exception|\Error $e) {
            Log::error($e->getTraceAsString());
            return false;
        }
    }
}
