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
            [
                'code' => 'PT011',
                'nickname' => 'Adi',
                'description' => 'Spesialis dalam pelatihan kekuatan dan daya tahan.',
                'metadata' => '{"experience_years": 13, "specialties": ["HIIT", "endurance"]}',
                'images' => ['default.jpg'],
                'user_personal_trainer_id' => 11,
            ],
            [
                'code' => 'PT012',
                'nickname' => 'Bella',
                'description' => 'Berpengalaman dalam program penurunan berat badan.',
                'metadata' => '{"experience_years": 1, "specialties": ["nutrition"]}',
                'images' => ['default.jpg'],
                'user_personal_trainer_id' => 12,
            ],
            [
                'code' => 'PT013',
                'nickname' => 'Chandra',
                'description' => 'Ahli dalam latihan fleksibilitas dan mobilitas.',
                'metadata' => '{"experience_years": 10, "specialties": ["endurance", "cardio", "weight loss"]}',
                'images' => ['default.jpg'],
                'user_personal_trainer_id' => 13,
            ],
            [
                'code' => 'PT014',
                'nickname' => 'Dian',
                'description' => 'Menyediakan pelatihan untuk pemulihan cedera.',
                'metadata' => '{"experience_years": 1, "specialties": ["endurance"]}',
                'images' => ['default.jpg'],
                'user_personal_trainer_id' => 14,
            ],
            [
                'code' => 'PT015',
                'nickname' => 'Edo',
                'description' => 'Terfokus pada pengembangan kebugaran anak-anak.',
                'metadata' => '{"experience_years": 15, "specialties": ["endurance"]}',
                'images' => ['default.jpg'],
                'user_personal_trainer_id' => 15,
            ],
            [
                'code' => 'PT016',
                'nickname' => 'Fitri',
                'description' => 'Pelatih pribadi dengan pendekatan holistik.',
                'metadata' => '{"experience_years": 15, "specialties": ["flexibility", "rehab", "weight loss"]}',
                'images' => ['default.jpg'],
                'user_personal_trainer_id' => 16,
            ],
            [
                'code' => 'PT017',
                'nickname' => 'Galih',
                'description' => 'Ahli nutrisi dan kebugaran menyeluruh.',
                'metadata' => '{"experience_years": 14, "specialties": ["yoga"]}',
                'images' => ['default.jpg'],
                'user_personal_trainer_id' => 17,
            ],
            [
                'code' => 'PT018',
                'nickname' => 'Hana',
                'description' => 'Berpengalaman dengan atlet profesional.',
                'metadata' => '{"experience_years": 13, "specialties": ["yoga", "strength"]}',
                'images' => ['default.jpg'],
                'user_personal_trainer_id' => 18,
            ],
            [
                'code' => 'PT019',
                'nickname' => 'Ilham',
                'description' => 'Memiliki pendekatan unik untuk latihan HIIT.',
                'metadata' => '{"experience_years": 14, "specialties": ["mobility", "endurance", "nutrition"]}',
                'images' => ['default.jpg'],
                'user_personal_trainer_id' => 19,
            ],
            [
                'code' => 'PT020',
                'nickname' => 'Jeni',
                'description' => 'Membantu klien mencapai tujuan kebugaran jangka panjang.',
                'metadata' => '{"experience_years": 13, "specialties": ["HIIT"]}',
                'images' => ['default.jpg'],
                'user_personal_trainer_id' => 20,
            ],
        ];

        foreach ($trainers as $trainer) {
            PersonalTrainer::create($trainer);
        }
    }
}
