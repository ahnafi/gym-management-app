<?php

namespace Database\Factories;

use App\Models\PersonalTrainerAssignment;
use App\Models\User;
use App\Models\PersonalTrainer;
use App\Models\PersonalTrainerPackage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PersonalTrainerAssignment>
 */
class PersonalTrainerAssignmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PersonalTrainerAssignment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sessionCount = $this->faker->numberBetween(8, 24);
        $remainingSessions = $this->faker->numberBetween(0, $sessionCount);
        $startDate = $this->faker->dateTimeBetween('-30 days', 'now');
        $endDate = Carbon::parse($startDate)->addDays($this->faker->numberBetween(60, 180));

        return [
            'user_id' => User::factory(),
            'personal_trainer_id' => PersonalTrainer::factory(),
            'personal_trainer_package_id' => PersonalTrainerPackage::factory(),
            'session_count' => $sessionCount,
            'remaining_sessions' => $remainingSessions,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement(['active', 'completed', 'cancelled']),
            'day_left' => Carbon::parse($endDate)->diffInDays(now()),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the assignment is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_date' => now()->subDays(10),
            'end_date' => now()->addDays(50),
            'remaining_sessions' => $this->faker->numberBetween(1, $attributes['session_count']),
        ]);
    }

    /**
     * Indicate that the assignment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'remaining_sessions' => 0,
            'end_date' => now()->subDays(5),
        ]);
    }

    /**
     * Indicate that the assignment is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'end_date' => now()->subDays(1),
        ]);
    }
}