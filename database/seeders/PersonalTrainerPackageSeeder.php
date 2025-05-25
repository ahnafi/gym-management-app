<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PersonalTrainerPackage;

class PersonalTrainerPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Basic Fitness Coaching',
                'description' => 'Program dasar untuk pemula dalam kebugaran.',
                'day_duration' => 7,
                'price' => 300000,
                'images' => ['default.jpg'],
                'personal_trainer_id' => 1,
            ],
            [
                'name' => 'Weight Loss Program',
                'description' => 'Program personal trainer untuk penurunan berat badan.',
                'day_duration' => 14,
                'price' => 600000,
                'images' => ['default.jpg'],
                'personal_trainer_id' => 2,
            ],
            [
                'name' => 'Muscle Building Plan',
                'description' => 'Fokus pada pembentukan massa otot.',
                'day_duration' => 30,
                'price' => 1200000,
                'images' => ['default.jpg'],
                'personal_trainer_id' => 3,
            ],
            [
                'name' => 'Cardio Booster',
                'description' => 'Program peningkatan stamina dan kebugaran jantung.',
                'day_duration' => 10,
                'price' => 400000,
                'images' => ['default.jpg'],
                'personal_trainer_id' => 1,
            ],
            [
                'name' => 'Post Injury Recovery',
                'description' => 'Program khusus untuk pemulihan pasca cedera.',
                'day_duration' => 21,
                'price' => 900000,
                'images' => ['default.jpg'],
                'personal_trainer_id' => 2,
            ],
            [
                'name' => 'Strength Training',
                'description' => 'Latihan kekuatan intensif untuk peningkatan performa.',
                'day_duration' => 30,
                'price' => 1300000,
                'images' => ['default.jpg'],
                'personal_trainer_id' => 3,
            ],
            [
                'name' => 'Flexibility Training',
                'description' => 'Fokus pada peningkatan fleksibilitas dan mobilitas tubuh.',
                'day_duration' => 10,
                'price' => 350000,
                'images' => ['default.jpg'],
                'personal_trainer_id' => 1,
            ],
            [
                'name' => 'Senior Fitness',
                'description' => 'Program kebugaran untuk lansia.',
                'day_duration' => 15,
                'price' => 550000,
                'images' => ['default.jpg'],
                'personal_trainer_id' => 2,
            ],
            [
                'name' => 'Youth Sports Prep',
                'description' => 'Latihan untuk pelajar dalam mempersiapkan kompetisi olahraga.',
                'day_duration' => 20,
                'price' => 800000,
                'images' => ['default.jpg'],
                'personal_trainer_id' => 3,
            ],
            [
                'name' => 'Prenatal Fitness',
                'description' => 'Program khusus untuk ibu hamil menjaga kebugaran.',
                'day_duration' => 14,
                'price' => 650000,
                'images' => ['default.jpg'],
                'personal_trainer_id' => 1,
            ],
        ];

        foreach ($packages as $package) {
            PersonalTrainerPackage::create($package);
        }
    }
}
