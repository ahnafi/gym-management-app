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
                'name' => 'Zumba Dance',
                'description' => 'Kelas kardio dengan gerakan menari energik.',
                'price' => 50000,
                'images' => 'classes/zumba.jpg',
            ],
            [
                'name' => 'Yoga Flow',
                'description' => 'Kelas yoga untuk meningkatkan fleksibilitas dan relaksasi.',
                'price' => 60000,
                'images' => 'classes/yoga.jpg',
            ],
            [
                'name' => 'Body Pump',
                'description' => 'Kelas latihan beban intensitas tinggi.',
                'price' => 70000,
                'images' => 'classes/bodypump.jpg',
            ],
            [
                'name' => 'HIIT Workout',
                'description' => 'Latihan interval intensitas tinggi untuk membakar kalori lebih cepat.',
                'price' => 75000,
                'images' => 'classes/hiit.jpg',
            ],
            [
                'name' => 'Pilates Core',
                'description' => 'Latihan untuk kekuatan otot inti dan postur tubuh.',
                'price' => 65000,
                'images' => 'classes/pilates.jpg',
            ],
            [
                'name' => 'Boxing Fit',
                'description' => 'Kelas latihan tinju untuk kebugaran dan kekuatan.',
                'price' => 80000,
                'images' => 'classes/boxing.jpg',
            ],
            [
                'name' => 'Cycling Spin',
                'description' => 'Kelas bersepeda dalam ruangan dengan irama musik.',
                'price' => 55000,
                'images' => 'classes/cycling.jpg',
            ],
            [
                'name' => 'Aqua Aerobics',
                'description' => 'Latihan aerobik di dalam air untuk semua usia.',
                'price' => 60000,
                'images' => 'classes/aqua.jpg',
            ],
            [
                'name' => 'CrossFit Circuit',
                'description' => 'Kelas kekuatan dan daya tahan dengan latihan sirkuit.',
                'price' => 85000,
                'images' => 'classes/crossfit.jpg',
            ],
            [
                'name' => 'Stretch & Relax',
                'description' => 'Kelas peregangan dan meditasi untuk relaksasi otot.',
                'price' => 45000,
                'images' => 'classes/stretch.jpg',
            ],
        ];

        foreach ($gymClasses as $class) {
            \App\Models\GymClass::create($class);
        }
    }
}
