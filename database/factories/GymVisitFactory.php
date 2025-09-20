<?php

namespace Database\Factories;

use App\Models\GymVisit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GymVisitFactory extends Factory
{
    protected $model = GymVisit::class;

    public function definition(): array
    {
        $visitDate = fake()->dateTimeBetween('-30 days', 'now');
        $entryTime = fake()->time();
        $exitTime = fake()->optional(0.7)->time(); // 70% chance of having exit time
        
        return [
            'user_id' => User::factory(),
            'visit_date' => $visitDate->format('Y-m-d'),
            'entry_time' => $entryTime,
            'exit_time' => $exitTime,
            'status' => $exitTime ? 'left' : 'in_gym',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function checkedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'exit_time' => null,
            'status' => 'in_gym',
        ]);
    }

    public function completed(): static
    {
        $entryTime = fake()->time();
        $exitTime = fake()->time();
        
        return $this->state(fn (array $attributes) => [
            'entry_time' => $entryTime,
            'exit_time' => $exitTime,
            'status' => 'left',
        ]);
    }

    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'visit_date' => now()->format('Y-m-d'),
        ]);
    }
}