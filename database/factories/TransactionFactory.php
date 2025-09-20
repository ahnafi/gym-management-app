<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use App\Models\MembershipPackage;
use App\Models\GymClass;
use App\Models\PersonalTrainerPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $purchasableTypes = [
            'App\Models\MembershipPackage',
            'App\Models\GymClass', 
            'App\Models\PersonalTrainerPackage',
        ];
        
        $purchasableType = $this->faker->randomElement($purchasableTypes);
        $purchasableId = 1; // Default to 1 for testing
        
        return [
            'code' => 'TTX-' . now()->format('Ymd') . '-U' . $this->faker->numberBetween(1, 999) . '-' . strtoupper($this->faker->bothify('??##')),
            'user_id' => UserFactory::new(),
            'gym_class_schedule_id' => null,
            'amount' => $this->faker->numberBetween(100000, 2000000),
            'snap_token' => $this->faker->uuid,
            'payment_date' => $this->faker->optional()->dateTimeBetween('-30 days', 'now'),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed']),
            'purchasable_type' => $purchasableType,
            'purchasable_id' => $purchasableId,
        ];
    }

    private function getItemId($type)
    {
        switch ($type) {
            case 'membership':
                return MembershipPackage::factory()->create()->id;
            case 'gym_class':
                return GymClass::factory()->create()->id;
            case 'personal_trainer':
                return PersonalTrainerPackage::factory()->create()->id;
            default:
                return null;
        }
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'paid_at' => null,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'paid_at' => null,
        ]);
    }

    public function membership(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'membership',
            'item_id' => MembershipPackage::factory()->create()->id,
        ]);
    }

    public function gymClass(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'gym_class',
            'item_id' => GymClass::factory()->create()->id,
        ]);
    }

    public function personalTrainer(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'personal_trainer',
            'item_id' => PersonalTrainerPackage::factory()->create()->id,
        ]);
    }
}