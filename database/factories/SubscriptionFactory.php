<?php

namespace Database\Factories;

use App\Enums\TimePeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pair' => $this->faker->word,
            'email' => $this->faker->email,
            'price' => $this->faker->randomFloat(2, 1, 100),
            'period' => $this->faker->randomElement(collect(TimePeriod::cases())->pluck('value')->toArray()),
            'percentage' => $this->faker->numberBetween(1, 100),
            'has_expired' => $this->faker->boolean,
            'valid_from' => $this->faker->dateTimeBetween('-10 hours', 'now')
        ];
    }

    public function expired(): self
    {
        return $this->state([
            'has_expired' => true
        ]);
    }

    public function active(): self
    {
        return $this->state([
            'has_expired' => false,
            'period' => TimePeriod::Hours6->value,
            'valid_from' => now()->subHour()->startOfHour()
        ]);
    }
}
