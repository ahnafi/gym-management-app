<?php

namespace Database\Factories;

use App\Models\MembershipPackage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MembershipPackageFactory extends Factory
{
    protected $model = MembershipPackage::class;

    public function definition(): array
    {
        $name = fake()->words(2, true) . ' Membership';
        
        return [
            'code' => 'MP' . fake()->unique()->numberBetween(10, 99),
            'name' => $name,
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(100000, 2000000),
            'duration' => fake()->numberBetween(30, 365),
            'status' => 'active',
            'images' => 'default-package.jpg',
            'slug' => Str::slug($name),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function basic(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Basic Membership',
            'price' => 150000,
            'duration_days' => 30,
            'max_gym_visits' => 8,
            'max_gym_classes' => 4,
            'personal_trainer_sessions' => 0,
        ]);
    }

    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Premium Membership',
            'price' => 500000,
            'duration_days' => 90,
            'max_gym_visits' => null,
            'max_gym_classes' => null,
            'personal_trainer_sessions' => 8,
        ]);
    }
}