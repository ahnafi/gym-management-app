<?php

namespace Database\Factories;

use App\Models\PersonalTrainer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonalTrainerFactory extends Factory
{
    protected $model = PersonalTrainer::class;

    public function definition(): array
    {
        return [
            'code' => 'PT' . $this->faker->unique()->randomNumber(4),
            'user_personal_trainer_id' => UserFactory::new(),
            'nickname' => $this->faker->name,
            'description' => $this->faker->text(200),
            'slug' => $this->faker->slug,
            'metadata' => [
                'specialization' => $this->faker->randomElement(['Yoga', 'Pilates', 'Weight Training', 'Cardio']),
                'experience_years' => $this->faker->numberBetween(1, 15),
                'hourly_rate' => $this->faker->numberBetween(100000, 500000),
            ],
            'images' => 'trainer-default.jpg',
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
        ]);
    }

    public function experienced(): static
    {
        return $this->state(fn (array $attributes) => [
            'experience_years' => fake()->numberBetween(8, 15),
            'rating' => fake()->randomFloat(1, 4.5, 5.0),
            'total_clients' => fake()->numberBetween(20, 50),
        ]);
    }

    public function newbie(): static
    {
        return $this->state(fn (array $attributes) => [
            'experience_years' => fake()->numberBetween(1, 3),
            'rating' => fake()->randomFloat(1, 3.0, 4.0),
            'total_clients' => fake()->numberBetween(0, 10),
        ]);
    }
}