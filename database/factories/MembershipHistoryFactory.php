<?php

namespace Database\Factories;

use App\Models\MembershipHistory;
use App\Models\User;
use App\Models\MembershipPackage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MembershipHistory>
 */
class MembershipHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MembershipHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-30 days', 'now');
        $endDate = Carbon::parse($startDate)->addDays($this->faker->numberBetween(30, 365));

        return [
            'user_id' => User::factory(),
            'membership_package_id' => MembershipPackage::factory(),
            'code' => 'MH' . $this->faker->unique()->numberBetween(10, 99),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement(['active', 'expired']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the membership is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(20),
        ]);
    }

    /**
     * Indicate that the membership is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'start_date' => now()->subDays(60),
            'end_date' => now()->subDays(10),
        ]);
    }

    /**
     * Indicate that the membership is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'start_date' => now()->subDays(30),
            'end_date' => now()->subDays(5),
        ]);
    }
}