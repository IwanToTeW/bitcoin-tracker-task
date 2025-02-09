<?php

namespace App\Http\Controllers;

use App\Actions\BitFinex\GetDayViewOptions;
use App\Actions\BitFinex\GetHistoryData;
use App\Enums\CurrencyPair;
use App\Enums\ViewInterval;
use App\Http\Requests\DashboardRequest;
use App\Http\Resources\EnumResource;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(DashboardRequest $request): Response
    {
        $data = (new GetHistoryData())->execute($request->validated());

        return Inertia::render('Dashboard',
            [
                'labels' => $data->pluck('date'),
                'data' => $data->pluck('mid'),
                'queryParams' => [
                  'pair' => $request->input('pair') ?? 'tBTCUSD',
                  'interval' => $request->input('interval') ?? 'day',
                ],
                'pairs' => EnumResource::collection(CurrencyPair::cases()),
                'intervals' =>  EnumResource::collection(ViewInterval::cases()),
            ]);
    }
}
