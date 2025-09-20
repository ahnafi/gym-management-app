<?php

namespace Database\Factories;

use App\Models\PersonalTrainerPackage;
use App\Models\PersonalTrainer;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonalTrainerPackageFactory extends Factory
{
    protected $model = PersonalTrainerPackage::class;

    public function definition(): array
    {
        return [
            'personal_trainer_id' => PersonalTrainer::factory(),
            'name' => fake()->words(2, true) . ' Training Package',
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(500000, 3000000),
            'session_count' => fake()->randomElement([4, 8, 12, 16]),
            'duration_days' => fake()->randomElement([30, 60, 90]),
            'is_active' => fake()->boolean(90),
            'includes_nutrition' => fake()->boolean(30),
            'includes_meal_plan' => fake()->boolean(20),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function basic(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Basic Training Package',
            'session_count' => 4,
            'duration_days' => 30,
            'includes_nutrition' => false,
            'includes_meal_plan' => false,
        ]);
    }

    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Premium Training Package',
            'session_count' => 16,
            'duration_days' => 90,
            'includes_nutrition' => true,
            'includes_meal_plan' => true,
        ]);
    }
}