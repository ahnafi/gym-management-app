<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MembershipPackage;

class MembershipPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Silver Package',
                'description' => 'Paket keanggotaan dasar dengan akses gym standar.',
                'duration' => 30, // hari
                'price' => 200000,
                'status' => 'active',
                'images' => ['default.jpg'],
            ],
            [
                'name' => 'Gold Package',
                'description' => 'Paket dengan akses lengkap ke semua fasilitas gym dan kelas.',
                'duration' => 30,
                'price' => 350000,
                'status' => 'active',
                'images' => ['default.jpg'],
            ],
            [
                'name' => 'Platinum Package',
                'description' => 'Paket premium dengan fasilitas VIP dan sesi personal trainer.',
                'duration' => 30,
                'price' => 500000,
                'status' => 'active',
                'images' => ['default.jpg'],
            ],
            [
                'name' => 'Student Package',
                'description' => 'Paket khusus pelajar dengan harga terjangkau.',
                'duration' => 30,
                'price' => 150000,
                'status' => 'active',
                'images' => ['default.jpg'],
            ],
            [
                'name' => 'Quarterly Package',
                'description' => 'Paket keanggotaan selama 3 bulan.',
                'duration' => 90,
                'price' => 900000,
                'status' => 'inactive',
                'images' => ['default.jpg'],
            ],
            [
                'name' => 'Annual Package',
                'description' => 'Paket tahunan dengan diskon spesial.',
                'duration' => 365,
                'price' => 3000000,
                'status' => 'active',
                'images' => ['default.jpg'],
            ],
        ];

        foreach ($packages as $package) {
            MembershipPackage::create($package);
        }
    }
}
