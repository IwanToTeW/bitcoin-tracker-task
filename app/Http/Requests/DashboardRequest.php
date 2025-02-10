<?php

namespace App\Http\Requests;

use App\Enums\CurrencyPair;
use App\Enums\ViewInterval;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class DashboardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currencyPairValues = array_map(fn($case) => $case->value, CurrencyPair::cases());
        $viewIntervalValues = array_map(fn($case) => $case->value, ViewInterval::cases());

        return [
            'pair' => ['nullable', 'string', Rule::in($currencyPairValues)],
            'interval' => ['nullable', 'string', Rule::in($viewIntervalValues)],
            'date' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }

    public function prepareForValidation(): void
    {
        if (empty($this->date)) {
            return;
        }

        try {
            $date = Carbon::parse($this->date);

            if ($date->isFuture()) {
                $this->merge([
                    'date' => Carbon::now()->format('Y-m-d')
                ]);
            }
        } catch (InvalidFormatException $e) {
            return;
        }
    }
}
