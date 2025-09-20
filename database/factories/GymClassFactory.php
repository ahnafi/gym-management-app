<?php

namespace Database\Factories;

use App\Models\GymClass;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GymClassFactory extends Factory
{
    protected $model = GymClass::class;

    public function definition(): array
    {
        $name = fake()->randomElement(['Yoga', 'Pilates', 'Body Combat', 'Spinning', 'Zumba']);
        
        return [
            'code' => 'GC' . fake()->unique()->numberBetween(10, 99),
            'name' => $name,
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(50000, 300000),
            'status' => 'active',
            'images' => 'default-class.jpg',
            'slug' => Str::slug($name),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function yoga(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Yoga Class',
        ]);
    }

    public function crossfit(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'CrossFit Training',
        ]);
    }
}