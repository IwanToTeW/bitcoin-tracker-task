<?php

namespace App\Http\Controllers;

use App\Actions\Subscriptions\StoreSubscription;
use App\Http\Requests\StoreSubscriptionRequest;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function store(StoreSubscriptionRequest $request, StoreSubscription $action): JsonResponse
    {
        return response()->json([
            'success' => $action->execute($request->validated()),
        ]);
    }
}
