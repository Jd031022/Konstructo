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
        // Clear existing users first (optional - be careful!)
        // User::truncate(); // Uncomment if you want to clear all users first

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

        // Create Staff (formerly Engineers)
        $staff = [
            [
                'first_name' => 'John',
                'last_name' => 'Santos',
                'email' => 'john.santos@konstructo.com',
                'username' => 'john.santos',
                'phone_number' => '09234567890',
                'department' => 'Engineering', // Added department for staff
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Reyes',
                'email' => 'maria.reyes@konstructo.com',
                'username' => 'maria.reyes',
                'phone_number' => '09345678901',
                'department' => 'Engineering',
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Lim',
                'email' => 'robert.lim@konstructo.com',
                'username' => 'robert.lim',
                'phone_number' => '09456789012',
                'department' => 'Engineering',
            ],
            [
                'first_name' => 'Patricia',
                'last_name' => 'Villanueva',
                'email' => 'patricia.v@konstructo.com',
                'username' => 'patricia.v',
                'phone_number' => '09567890123',
                'department' => 'Engineering',
            ],
            // Additional staff from other departments
            [
                'first_name' => 'Michael',
                'last_name' => 'Tan',
                'email' => 'michael.tan@konstructo.com',
                'username' => 'michael.tan',
                'phone_number' => '09678901234',
                'department' => 'Customer Support',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Gonzales',
                'email' => 'sarah.gonzales@konstructo.com',
                'username' => 'sarah.gonzales',
                'phone_number' => '09789012345',
                'department' => 'Administration',
            ],
        ];

        foreach ($staff as $staffMember) {
            User::create([
                'first_name' => $staffMember['first_name'],
                'last_name' => $staffMember['last_name'],
                'email' => $staffMember['email'],
                'username' => $staffMember['username'],
                'password' => Hash::make('Staff123!@#'),
                'role' => 'staff',
                'phone_number' => $staffMember['phone_number'],
                'address' => $staffMember['department'] . ' Department', // Dynamic address based on department
                'zip_code' => '1000',
                'email_verified_at' => now(),
            ]);
        }

        // Create Applicants
        $applicants = [
            [
                'first_name' => 'Anna',
                'last_name' => 'Lopez',
                'email' => 'anna.lopez@email.com',
                'username' => 'anna.lopez',
                'phone_number' => '09678901234',
            ],
            [
                'first_name' => 'Mark',
                'last_name' => 'Garcia',
                'email' => 'mark.garcia@email.com',
                'username' => 'mark.garcia',
                'phone_number' => '09789012345',
            ],
            [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'email' => 'juan.delacruz@email.com',
                'username' => 'juan.delacruz',
                'phone_number' => '09890123456',
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'email' => 'maria.santos@email.com',
                'username' => 'maria.santos',
                'phone_number' => '09901234567',
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
                'phone_number' => $applicant['phone_number'],
                'address' => 'Applicant Address',
                'zip_code' => '1000',
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('Users seeded successfully!');
        $this->command->info('Created: 1 Admin, ' . count($staff) . ' Staff, and ' . count($applicants) . ' Applicants');
    }
}