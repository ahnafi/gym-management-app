<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PersonalTrainer;

class PersonalTrainerSeeder extends Seeder
{
    public function run(): void
    {
        $trainers = [
            [
                'code' => 'PT003',
                'nickname' => 'Citra',
                'description' => 'Fokus pada wanita dan kebugaran pasca melahirkan.',
                'metadata' => json_encode(['experience_years' => 6, 'specialties' => ['prenatal', 'postnatal']]),
                'images' => ['default.jpg'],
                'user_personal_trainer_id' => 3,
            ],
            [
                'code' => 'PT004',
                'nickname' => 'Dewi',
                'description' => 'Ahli yoga dan fleksibilitas.',
                'metadata' => json_encode(['experience_years' => 7, 'specialties' => ['yoga', 'mobility']]),
                'images' => ['default.jpg'],
                'user_personal_trainer_id' => 4,
            ],
            [
                'code' => 'PT005',
                'nickname' => 'Eka',
                'description' => 'Pelatih kebugaran untuk lansia.',
                'metadata' => json_encode(['experience_years' => 10, 'specialties' => ['senior fitness']]),
                'images' => ['default.jpg'],
                'user_personal_trainer_id' => 5,
            ],
            [
                'code' => 'PT006',
                'nickname' => 'Fajar',
                'description' => 'Pelatih fungsional dan HIIT.',
                'metadata' => json_encode(['experience_years' => 3, 'specialties' => ['HIIT', 'functional training']]),
                'images' => ['default.jpg'],
                'user_personal_trainer_id' => 6,
            ],
            [
                'code' => 'PT007',
                'nickname' => 'Gita',
                'description' => 'Spesialis pelatihan atlet muda.',
                'metadata' => json_encode(['experience_years' => 4, 'specialties' => ['youth athletics']]),
                'images' => ['default.jpg'],
                'user_personal_trainer_id' => 7,
            ],

            [
                'code' => 'PT008',
                'nickname' => 'Indah',
                'description' => 'Fokus pelatihan wanita muda.',
                'metadata' => json_encode(['experience_years' => 2, 'specialties' => ['beginner female fitness']]),
                'images' => ['default.jpg'],
                'user_personal_trainer_id' => 9,
            ],
            [
                'code' => 'PT09',
                'nickname' => 'Joko',
                'description' => 'Pelatih senior dengan sertifikasi internasional.',
                'metadata' => json_encode(['experience_years' => 12, 'certified' => true]),
                'images' => ['default.jpg'],
                'user_personal_trainer_id' => 10,
            ],
        ];

        foreach ($trainers as $trainer) {
            PersonalTrainer::create($trainer);
        }
    }
}
