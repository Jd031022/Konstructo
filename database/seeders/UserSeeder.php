<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Admin
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@konstructo.com',
            'username' => 'admin',
            'password' => Hash::make('Admin123!@#'),
            'role' => 'admin',
            'phone_number' => '09123456789',
            'address' => 'Admin Office, Main Street',
            'zip_code' => '1000',
            'email_verified_at' => now(),
        ]);

        // Create Engineers
        $engineers = [
            [
                'first_name' => 'John',
                'last_name' => 'Santos',
                'email' => 'john.santos@konstructo.com',
                'username' => 'john.santos',
                'phone' => '09234567890',
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Reyes',
                'email' => 'maria.reyes@konstructo.com',
                'username' => 'maria.reyes',
                'phone' => '09345678901',
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Lim',
                'email' => 'robert.lim@konstructo.com',
                'username' => 'robert.lim',
                'phone' => '09456789012',
            ],
            [
                'first_name' => 'Patricia',
                'last_name' => 'Villanueva',
                'email' => 'patricia.v@konstructo.com',
                'username' => 'patricia.v',
                'phone' => '09567890123',
            ],
        ];

        foreach ($engineers as $engineer) {
            User::create([
                'first_name' => $engineer['first_name'],
                'last_name' => $engineer['last_name'],
                'email' => $engineer['email'],
                'username' => $engineer['username'],
                'password' => Hash::make('Engineer123!@#'),
                'role' => 'engineer',
                'phone_number' => $engineer['phone'],
                'address' => 'Engineering Department',
                'zip_code' => '1000',
                'email_verified_at' => now(),
            ]);
        }

        // Create Applicants (for testing)
        $applicants = [
            [
                'first_name' => 'Anna',
                'last_name' => 'Lopez',
                'email' => 'anna.lopez@email.com',
                'username' => 'anna.lopez',
                'phone' => '09678901234',
            ],
            [
                'first_name' => 'Mark',
                'last_name' => 'Garcia',
                'email' => 'mark.garcia@email.com',
                'username' => 'mark.garcia',
                'phone' => '09789012345',
            ],
        ];

        foreach ($applicants as $applicant) {
            User::create([
                'first_name' => $applicant['first_name'],
                'last_name' => $applicant['last_name'],
                'email' => $applicant['email'],
                'username' => $applicant['username'],
                'password' => Hash::make('Applicant123!@#'),
                'role' => 'applicant',
                'phone_number' => $applicant['phone'],
                'address' => 'Applicant Address',
                'zip_code' => '1000',
                'email_verified_at' => now(),
            ]);
        }
    }
}