<?php

namespace Database\Factories;

use App\Models\GymClassAttendance;
use App\Models\User;
use App\Models\GymClassSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class GymClassAttendanceFactory extends Factory
{
    protected $model = GymClassAttendance::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'gym_class_schedule_id' => GymClassSchedule::factory(),
            'status' => fake()->randomElement(['assigned', 'attended', 'missed']),
            'attended_at' => fake()->optional(0.6)->dateTimeBetween('-1 week', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function attended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'attended',
            'attended_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    public function missed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'missed',
            'attended_at' => null,
        ]);
    }

    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'assigned',
            'attended_at' => null,
        ]);
    }
}