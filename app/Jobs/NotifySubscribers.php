<?php

namespace App\Jobs;

use App\Actions\BitFinex\GetLastHourData;
use App\Actions\Subscriptions\CheckForPriceDeviation;
use App\Enums\CurrencyPair;
use App\Enums\TimePeriod;
use App\Models\Subscription;
use App\Notifications\PriceReachedLimit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class NotifySubscribers implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(GetLastHourData $getLastHourData, CheckForPriceDeviation $checkForPriceDeviation): void
    {
        $usd = $getLastHourData->execute();
        $eur = $getLastHourData->execute(CurrencyPair::BTC_EURO->value);

        $currentPrices = [
            CurrencyPair::BTC_USD->value => $usd['mid'],
            CurrencyPair::BTC_EURO->value => $eur['mid'],
        ];

        $subscriptions = Subscription::active()->cursor();
        $subscriptions->each(function (Subscription $subscription) use ($currentPrices, $checkForPriceDeviation) {

            if($subscription->period === TimePeriod::NotSpecified->value) {

                $result = $currentPrices[$subscription->pair] > $subscription->price;
                if($result) {
                    Notification::route('mail', $subscription->email)->notify(new PriceReachedLimit());
                    return;
                }
            }

            $hasExceededPrice = $checkForPriceDeviation->execute($subscription, $currentPrices);

            if($hasExceededPrice) {
                Notification::route('mail', $subscription->email)->notify(new PriceReachedLimit());
            }
        });
    }
}
