<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GymClass;

class GymClassSeeder extends Seeder
{
    public function run(): void
    {
        $gymClasses = [
            [
                'code' => 'GYM001',
                'name' => 'Zumba Dance',
                'description' => 'Kelas kardio dengan gerakan menari energik.',
                'price' => 50000,
                'images' => ['gym_class\/gc1.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM002',
                'name' => 'Yoga Flow',
                'description' => 'Kelas yoga untuk meningkatkan fleksibilitas dan relaksasi.',
                'price' => 60000,
                'images' => ['gym_class\/gc2.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM003',
                'name' => 'Body Pump',
                'description' => 'Kelas latihan beban intensitas tinggi.',
                'price' => 70000,
                'images' => ['gym_class\/gc3.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM004',
                'name' => 'HIIT Workout',
                'description' => 'Latihan interval intensitas tinggi untuk membakar kalori lebih cepat.',
                'price' => 75000,
                'images' => ['gym_class\/gc4.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM005',
                'name' => 'Pilates Core',
                'description' => 'Latihan untuk kekuatan otot inti dan postur tubuh.',
                'price' => 65000,
                'images' => ['gym_class\/gc5.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM006',
                'name' => 'Boxing Fit',
                'description' => 'Kelas latihan tinju untuk kebugaran dan kekuatan.',
                'price' => 80000,
                'images' => ['gym_class\/gc6.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM007',
                'name' => 'Cycling Spin',
                'description' => 'Kelas bersepeda dalam ruangan dengan irama musik.',
                'price' => 55000,
                'images' => ['gym_class\/gc7.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM008',
                'name' => 'Aqua Aerobics',
                'description' => 'Latihan aerobik di dalam air untuk semua usia.',
                'price' => 60000,
                'images' => ['gym_class\/gc8.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM009',
                'name' => 'CrossFit Circuit',
                'description' => 'Kelas kekuatan dan daya tahan dengan latihan sirkuit.',
                'price' => 85000,
                'images' => ['gym_class\/gc9.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM010',
                'name' => 'Stretch & Relax',
                'description' => 'Kelas peregangan dan meditasi untuk relaksasi otot.',
                'price' => 45000,
                'images' => ['gym_class\/gc10.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM011',
                'name' => 'Functional Training',
                'description' => 'Latihan fungsional untuk gerakan sehari-hari.',
                'price' => 70000,
                'images' => ['gym_class\/gc11.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM012',
                'name' => 'TRX Suspension',
                'description' => 'Latihan kekuatan menggunakan tali TRX.',
                'price' => 72000,
                'images' => ['gym_class\/gc12.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM013',
                'name' => 'Core Stability',
                'description' => 'Fokus pada kestabilan otot inti.',
                'price' => 68000,
                'images' => ['gym_class\/gc13.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM014',
                'name' => 'Barre Fitness',
                'description' => 'Gabungan ballet, yoga, dan pilates.',
                'price' => 75000,
                'images' => ['gym_class\/gc14.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM015',
                'name' => 'Tabata Express',
                'description' => 'Latihan HIIT singkat dan padat.',
                'price' => 73000,
                'images' => ['gym_class\/gc15.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM016',
                'name' => 'Dance Cardio',
                'description' => 'Kelas menari cepat untuk bakar kalori.',
                'price' => 50000,
                'images' => ['gym_class\/gc16.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM017',
                'name' => 'Kickboxing',
                'description' => 'Latihan bela diri untuk cardio dan kekuatan.',
                'price' => 79000,
                'images' => ['gym_class\/gc17.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM018',
                'name' => 'Bootcamp Blast',
                'description' => 'Latihan intens di luar ruangan.',
                'price' => 85000,
                'images' => ['gym_class\/gc18.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM019',
                'name' => 'Morning Mobility',
                'description' => 'Peregangan dan pemanasan pagi hari.',
                'price' => 46000,
                'images' => ['gym_class\/gc19.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM020',
                'name' => 'Evening Flow',
                'description' => 'Yoga dan meditasi malam hari.',
                'price' => 60000,
                'images' => ['gym_class\/gc20.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM021',
                'name' => 'Strength & Sculpt',
                'description' => 'Latihan kekuatan dan pembentukan otot.',
                'price' => 80000,
                'images' => ['gym_class\/gc21.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM022',
                'name' => 'Balance Training',
                'description' => 'Latihan koordinasi dan keseimbangan.',
                'price' => 69000,
                'images' => ['gym_class\/gc22.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM023',
                'name' => 'Power Yoga',
                'description' => 'Yoga dinamis untuk stamina dan fleksibilitas.',
                'price' => 77000,
                'images' => ['gym_class\/gc23.jpg'],
                'status' => 'active',
            ],
            [
                'code' => 'GYM024',
                'name' => 'Zen Meditation',
                'description' => 'TSesi meditasi untuk ketenangan pikiran.',
                'price' => 55000,
                'images' => ['gym_class\/gc24.jpg'],
                'status' => 'active',
            ],
        ];

        foreach ($gymClasses as $gymClass) {
            GymClass::create($gymClass);
        }
    }
}
