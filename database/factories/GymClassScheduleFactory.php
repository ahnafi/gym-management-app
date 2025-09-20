<?php

namespace Database\Factories;

use App\Models\GymClassSchedule;
use App\Models\GymClass;
use Illuminate\Database\Eloquent\Factories\Factory;

class GymClassScheduleFactory extends Factory
{
    protected $model = GymClassSchedule::class;

    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('now', '+1 month');
        $startTime = $this->faker->time('H:i:s');
        $endTime = $this->faker->time('H:i:s');
        
        return [
            'gym_class_id' => GymClassFactory::new(),
            'date' => $date->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'slot' => $this->faker->numberBetween(5, 20),
            'available_slot' => $this->faker->numberBetween(5, 20),
        ];
    }

    public function upcoming(): static
    {
        $date = fake()->dateTimeBetween('+1 hour', '+1 week');
        $startHour = fake()->numberBetween(7, 20);
        $startTime = sprintf('%02d:00:00', $startHour);
        $endHour = $startHour + 1;
        $endTime = sprintf('%02d:00:00', $endHour);

        return $this->state(fn (array $attributes) => [
            'date' => $date->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);
    }

    public function full(): static
    {
        return $this->state(fn (array $attributes) => [
            'slot' => 10,
            'available_slot' => 0,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'available_slot' => 0,
        ]);
    }
}