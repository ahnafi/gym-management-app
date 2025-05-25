<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // 2 Admin
            [
                'name' => 'Admin One',
                'email' => 'admin1@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'membership_registered' => 'registered',
                'membership_status' => 'active',
                'membership_end_date' => now()->addYear(),
                'profile_bio' => 'Primary system administrator.',
                'profile_image' => ['default.jpg'],
            ],
            [
                'name' => 'Admin Two',
                'email' => 'admin2@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'membership_registered' => 'registered',
                'membership_status' => 'active',
                'membership_end_date' => now()->addYear(),
                'profile_bio' => 'Backup system administrator.',
                'profile_image' => ['default.jpg'],
            ],

            // 5 Trainer
            [
                'name' => 'Trainer One',
                'email' => 'trainer1@example.com',
                'password' => Hash::make('password'),
                'role' => 'trainer',
                'membership_registered' => 'registered',
                'membership_status' => 'active',
                'membership_end_date' => now()->addMonths(6),
                'profile_bio' => 'Trainer ahli kebugaran.',
                'profile_image' => ['default.jpg'],
            ],
            [
                'name' => 'Trainer Two',
                'email' => 'trainer2@example.com',
                'password' => Hash::make('password'),
                'role' => 'trainer',
                'membership_registered' => 'registered',
                'membership_status' => 'active',
                'membership_end_date' => now()->addMonths(6),
                'profile_bio' => 'Spesialis latihan kekuatan.',
                'profile_image' => ['default.jpg'],
            ],
            [
                'name' => 'Trainer Three',
                'email' => 'trainer3@example.com',
                'password' => Hash::make('password'),
                'role' => 'trainer',
                'membership_registered' => 'registered',
                'membership_status' => 'active',
                'membership_end_date' => now()->addMonths(6),
                'profile_bio' => 'Pelatih HIIT bersertifikat.',
                'profile_image' => ['default.jpg'],
            ],
            [
                'name' => 'Trainer Four',
                'email' => 'trainer4@example.com',
                'password' => Hash::make('password'),
                'role' => 'trainer',
                'membership_registered' => 'registered',
                'membership_status' => 'active',
                'membership_end_date' => now()->addMonths(6),
                'profile_bio' => 'Ahli rehabilitasi dan mobilitas.',
                'profile_image' => ['default.jpg'],
            ],
            [
                'name' => 'Trainer Five',
                'email' => 'trainer5@example.com',
                'password' => Hash::make('password'),
                'role' => 'trainer',
                'membership_registered' => 'registered',
                'membership_status' => 'active',
                'membership_end_date' => now()->addMonths(6),
                'profile_bio' => 'Pelatih kebugaran umum.',
                'profile_image' => ['default.jpg'],
            ],

            // 5 Member (manual)
            [
                'name' => 'Member One',
                'email' => 'member1@example.com',
                'password' => Hash::make('password'),
                'role' => 'member',
                'membership_registered' => 'registered',
                'membership_status' => 'active',
                'membership_end_date' => now()->addMonths(3),
                'profile_bio' => 'Anggota aktif gym.',
                'profile_image' => ['default.jpg'],
            ],
            [
                'name' => 'Member Two',
                'email' => 'member2@example.com',
                'password' => Hash::make('password'),
                'role' => 'member',
                'membership_registered' => 'registered',
                'membership_status' => 'active',
                'membership_end_date' => now()->addMonths(3),
                'profile_bio' => 'Anggota baru bergabung.',
                'profile_image' => ['default.jpg'],
            ],
            [
                'name' => 'Member Three',
                'email' => 'member3@example.com',
                'password' => Hash::make('password'),
                'role' => 'member',
                'membership_registered' => 'registered',
                'membership_status' => 'active',
                'membership_end_date' => now()->addMonths(3),
                'profile_bio' => 'Sering mengikuti kelas yoga.',
                'profile_image' => ['default.jpg'],
            ],
            [
                'name' => 'Member Four',
                'email' => 'member4@example.com',
                'password' => Hash::make('password'),
                'role' => 'member',
                'membership_registered' => 'registered',
                'membership_status' => 'active',
                'membership_end_date' => now()->addMonths(3),
                'profile_bio' => 'Anggota pemula.',
                'profile_image' => ['default.jpg'],
            ],
            [
                'name' => 'Member Five',
                'email' => 'member5@example.com',
                'password' => Hash::make('password'),
                'role' => 'member',
                'membership_registered' => 'registered',
                'membership_status' => 'active',
                'membership_end_date' => now()->addMonths(3),
                'profile_bio' => 'Suka angkat beban.',
                'profile_image' => ['default.jpg'],
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        // Tambahan 5 member dari factory
        \App\Models\User::factory(5)->create([
            'role' => 'member',
            'membership_registered' => 'registered',
            'membership_status' => 'active',
        ]);
    }
}
