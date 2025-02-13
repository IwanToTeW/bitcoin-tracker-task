<?php

namespace App\Http\Requests;

use App\Enums\CurrencyPair;
use App\Models\User;
use App\Rules\IsPercentageRequired;
use App\Rules\IsPeriodRequired;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubscriptionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currencyPairValues = array_map(fn($case) => $case->value, CurrencyPair::cases());

        return [
            'pair' => ['required', 'string', Rule::in($currencyPairValues)],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'percentage' => ['nullable', 'numeric'],
            'period' => [new IsPeriodRequired, 'numeric', 'min:0'],
        ];
    }
}
