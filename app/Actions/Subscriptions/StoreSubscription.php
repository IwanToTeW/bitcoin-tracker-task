<?php

namespace App\Actions\Subscriptions;

use App\Models\Subscription;
use Illuminate\Support\Facades\Log;

class StoreSubscription
{
    public function execute(array $data): bool
    {
        try {
            $data['period'] = (string) $data['period'];
            $data['valid_from'] = now()->addHour()->startOfHour();
            $subscription = new Subscription($data);
            return $subscription->save();
        } catch (\Exception|\Error $e) {
            Log::error($e->getTraceAsString());
            return false;
        }
    }
}
